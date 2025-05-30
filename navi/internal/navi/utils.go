package navi

import (
	"encoding/hex"
	"fmt"
	"github.com/spf13/viper"
	"github.com/twpayne/go-geom"
	"github.com/twpayne/go-geom/encoding/ewkb"
	"github.com/twpayne/go-geom/encoding/wkt"
	"math"
)

func latLngToGlobalXY(lat, lng, avgLat float64) GlobalXY {
	radius := viper.GetFloat64("map.radius")
	x := radius * lng * math.Cos(avgLat*math.Pi/180)
	y := radius * lat
	return GlobalXY{x, y}
}

func latLngToScreenXY(lat, lng float64, p0, p1 GlobalPoint) (float64, float64) {
	pos := latLngToGlobalXY(lat, lng, (p0.lat+p1.lat)/2)

	perX := (pos.x - p0.pos.x) / (p1.pos.x - p0.pos.x)
	perY := (pos.y - p0.pos.y) / (p1.pos.y - p0.pos.y)

	x := p0.scrX + (p1.scrX-p0.scrX)*perX
	y := p0.scrY + (p1.scrY-p0.scrY)*perY

	return x, y
}

func GenerateWktLineString(start, end [2]float64, step float64) string {
	deltaX := end[0] - start[0]
	deltaY := end[1] - start[1]
	lineLength := math.Sqrt(deltaX*deltaX + deltaY*deltaY)

	steps := int(lineLength / step)

	var lineCoords [][2]float64
	for i := 0; i <= steps; i++ {
		x := start[0] + (deltaX*float64(i))/float64(steps)
		y := start[1] + (deltaY*float64(i))/float64(steps)
		lineCoords = append(lineCoords, [2]float64{x, y})
	}

	lineString := "LINESTRING("
	for i, cord := range lineCoords {
		lineString += fmt.Sprintf("%f %f", cord[0], cord[1])
		if i < len(lineCoords)-1 {
			lineString += ", "
		}
	}
	lineString += ")"

	return lineString
}

func GenerateWktPolygon(cords [][]Point) (string, error) {
	exterior := make([]geom.Coord, len(cords[0]))
	for i, cord := range cords[0] {
		exterior[i] = geom.Coord{cord.X, cord.Y}
	}

	polygonGeom := geom.NewPolygon(geom.XY).MustSetCoords([][]geom.Coord{exterior})

	wktString, err := wkt.Marshal(polygonGeom)
	if err != nil {
		return "", err
	}

	return wktString, nil
}

func ParseWkbPolygon(wkbHex string) ([][]Point, error) {
	wkbBytes, err := hex.DecodeString(wkbHex)
	if err != nil {
		return nil, fmt.Errorf("failed to decode hex: %v", err)
	}

	geomT, err := ewkb.Unmarshal(wkbBytes)
	if err != nil {
		return nil, fmt.Errorf("failed to unmarshal EWKB: %v", err)
	}

	polygon, ok := geomT.(*geom.Polygon)
	if !ok {
		return nil, fmt.Errorf("geometry is not a polygon")
	}

	coords := polygon.Coords()
	points := make([][]Point, len(coords))
	for i := range coords {
		points[i] = make([]Point, len(coords[i]))
		for j := range coords[i] {
			points[i][j] = Point{
				X: coords[i][j].X(),
				Y: coords[i][j].Y(),
			}
		}
	}

	return points, nil
}

func ParseWkbPoint(wkbHex string) (Point, error) {
	wkbBytes, err := hex.DecodeString(wkbHex)
	if err != nil {
		return Point{}, fmt.Errorf("failed to decode hex: %v", err)
	}

	geomT, err := ewkb.Unmarshal(wkbBytes)
	if err != nil {
		return Point{}, fmt.Errorf("failed to unmarshal EWKB: %v", err)
	}

	point, ok := geomT.(*geom.Point)
	if !ok {
		return Point{}, fmt.Errorf("geometry is not a point")
	}

	coords := point.Coords()
	return Point{
		X: coords.X(),
		Y: coords.Y(),
	}, nil
}

func GenerateMetersFromLatLon(lat, lon float64) (float64, float64) {
	p0 := GlobalPoint{
		scrX: viper.GetFloat64("map.top_left.x"),
		scrY: viper.GetFloat64("map.top_left.y"),
		lat:  viper.GetFloat64("map.top_left.lat"),
		lng:  viper.GetFloat64("map.top_left.lon"),
	}
	p1 := GlobalPoint{
		scrX: viper.GetFloat64("map.bot_right.x"),
		scrY: viper.GetFloat64("map.bot_right.y"),
		lat:  viper.GetFloat64("map.bot_right.lat"),
		lng:  viper.GetFloat64("map.bot_right.lon"),
	}

	avgLat := (p0.lat + p1.lat) / 2
	p0.pos = latLngToGlobalXY(p0.lat, p0.lng, avgLat)
	p1.pos = latLngToGlobalXY(p1.lat, p1.lng, avgLat)

	x, y := latLngToScreenXY(lat, lon, p0, p1)

	return x, y
}
