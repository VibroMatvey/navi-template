package navi

import (
	"bytes"
	"encoding/json"
	"fmt"
	"github.com/sirupsen/logrus"
	"github.com/spf13/viper"
	"math"
	"net/http"
)

type Service struct {
	repo *Repository
}

func NewService(repo *Repository) *Service {
	return &Service{repo: repo}
}

// Road

func (s *Service) GetRoads(mapId string) []RoadOutput {
	roads := s.repo.GetRoads(mapId)

	output := make([]RoadOutput, 0)
	for _, road := range roads {
		roadOutput := RoadOutput{
			Id:    road.ID,
			MapId: road.MapID,
		}

		sourceGeom, _ := s.repo.GetPoint(road.Source.TheGeom)
		roadOutput.Source = RoadOutputPoint{
			NodeId: road.Source.ID,
			X:      sourceGeom.X,
			Y:      sourceGeom.Y,
		}

		targetGeom, _ := s.repo.GetPoint(road.Target.TheGeom)
		roadOutput.Target = RoadOutputPoint{
			NodeId: road.Target.ID,
			X:      targetGeom.X,
			Y:      targetGeom.Y,
		}

		output = append(output, roadOutput)
	}

	return output
}

func (s *Service) CreateRoad(start, end Point, mapID string) (*RoadOutput, error) {
	lineString := GenerateWktLineString([2]float64{start.X, start.Y}, [2]float64{end.X, end.Y}, 1)

	road := &Road{
		Geom:  lineString,
		MapID: mapID,
	}

	road, err := s.repo.CreateRoad(road)
	if err != nil {
		return nil, err
	}

	return &RoadOutput{
		Id:    road.ID,
		MapId: road.MapID,
		Source: RoadOutputPoint{
			NodeId: road.Source.ID,
			X:      start.X,
			Y:      start.Y,
		},
		Target: RoadOutputPoint{
			NodeId: road.Target.ID,
			X:      end.X,
			Y:      end.Y,
		},
	}, nil
}

func (s *Service) UpdateRoad(start, end Point, roadId uint) (*RoadOutput, error) {
	road, err := s.repo.FindRoadById(roadId)
	if err != nil {
		return nil, err
	}
	lineString := GenerateWktLineString([2]float64{start.X, start.Y}, [2]float64{end.X, end.Y}, 1)
	road.Geom = lineString
	road, err = s.repo.UpdateRoad(road)
	if err != nil {
		return nil, err
	}
	return &RoadOutput{
		Id: road.ID,
		Source: RoadOutputPoint{
			NodeId: road.Source.ID,
			X:      start.X,
			Y:      start.Y,
		},
		Target: RoadOutputPoint{
			NodeId: road.Target.ID,
			X:      end.X,
			Y:      end.Y,
		},
	}, nil
}

func (s *Service) DeleteRoad(roadId uint) error {
	return s.repo.DeleteRoad(roadId)
}

// Area

func (s *Service) GetAreas() []AreaOutput {
	areas := s.repo.GetAreas()
	output := make([]AreaOutput, 0)
	for _, area := range areas {
		cords, err := ParseWkbPolygon(area.Geom)
		if err != nil {
			logrus.Info(err)
		}

		areaOutput := AreaOutput{
			Id:       area.ID,
			Cords:    cords,
			ObjectId: area.ObjectId,
			MapId:    area.MapId,
			NodeId:   area.Node.ID,
		}

		output = append(output, areaOutput)
	}

	return output
}

func (s *Service) CreateArea(cords [][]Point, objectId, mapID string, nodeId uint) (*AreaOutput, error) {
	if _, err := s.repo.FindAreaByObjectId(objectId); err == nil {
		return nil, fmt.Errorf("area with this objectId already exist")
	}
	if _, err := s.repo.FindMapPointByObjectId(objectId); err == nil {
		return nil, fmt.Errorf("map point with this objectId already exist")
	}

	wktString, err := GenerateWktPolygon(cords)
	if err != nil {
		return nil, fmt.Errorf("WKT convert error: %v", err)
	}

	area := &Area{
		Geom:     wktString,
		ObjectId: objectId,
		MapId:    mapID,
	}

	area, err = s.repo.CreateArea(area, nodeId)
	if err != nil {
		return nil, err
	}

	return &AreaOutput{
		Id:       area.ID,
		Cords:    cords,
		ObjectId: area.ObjectId,
		MapId:    area.MapId,
		NodeId:   area.Node.ID,
	}, nil
}

func (s *Service) UpdateArea(cords [][]Point, objectId string, areaId uint) (*AreaOutput, error) {
	area, err := s.repo.FindAreaById(areaId)
	if err != nil {
		return nil, err
	}

	if _, err := s.repo.FindAreaByObjectId(objectId); err == nil && area.ObjectId != objectId {
		return nil, fmt.Errorf("area with this objectId already exist")
	}
	if _, err := s.repo.FindMapPointByObjectId(objectId); err == nil && area.ObjectId != objectId {
		return nil, fmt.Errorf("map point with this objectId already exist")
	}

	wktString, err := GenerateWktPolygon(cords)
	if err != nil {
		return nil, fmt.Errorf("WKT convert error: %v", err)
	}

	area.Geom = wktString
	area.ObjectId = objectId

	area, err = s.repo.UpdateArea(area)
	if err != nil {
		return nil, err
	}

	return &AreaOutput{
		Id:       area.ID,
		Cords:    cords,
		ObjectId: area.ObjectId,
		MapId:    area.MapId,
		NodeId:   area.Node.ID,
	}, nil
}

func (s *Service) DeleteArea(areaId uint) error {
	return s.repo.DeleteArea(areaId)
}

// MapPoint

func (s *Service) GetMapPoints() []MapPointOutput {
	mapPoints := s.repo.GetMapPoints()
	output := make([]MapPointOutput, 0)
	for _, mapPoint := range mapPoints {
		point, err := s.repo.GetPoint(mapPoint.Geom)
		if err != nil {
			logrus.Info(err)
		}

		mapPointOutput := MapPointOutput{
			Id:       mapPoint.ID,
			X:        point.X,
			Y:        point.Y,
			ObjectId: mapPoint.ObjectId,
			MapId:    mapPoint.MapId,
			NodeId:   mapPoint.Node.ID,
		}

		output = append(output, mapPointOutput)
	}

	return output
}

func (s *Service) CreateMapPoint(x, y float64, mapId, objectId string, nodeId uint) (*MapPointOutput, error) {
	if _, err := s.repo.FindAreaByObjectId(objectId); err == nil {
		return nil, fmt.Errorf("area with this objectId already exist")
	}
	if _, err := s.repo.FindMapPointByObjectId(objectId); err == nil {
		return nil, fmt.Errorf("map point with this objectId already exist")
	}

	geom, err := s.repo.GetGeomFromPoint(x, y)
	if err != nil {
		return nil, err
	}

	mapPoint := &MapPoint{
		Geom:     *geom,
		MapId:    mapId,
		ObjectId: objectId,
	}

	mapPoint, err = s.repo.CreateMapPoint(mapPoint, nodeId)
	if err != nil {
		return nil, err
	}

	point, err := s.repo.GetPoint(mapPoint.Geom)
	if err != nil {
		return nil, err
	}

	return &MapPointOutput{
		Id:       mapPoint.ID,
		X:        point.X,
		Y:        point.Y,
		MapId:    mapPoint.MapId,
		ObjectId: mapPoint.ObjectId,
		NodeId:   mapPoint.Node.ID,
	}, nil
}

func (s *Service) UpdateMapPoint(x, y float64, objectId string, mapPointId uint) (*MapPointOutput, error) {
	mapPoint, err := s.repo.FindMapPointById(mapPointId)
	if err != nil {
		return nil, err
	}
	if _, err := s.repo.FindMapPointByObjectId(objectId); err == nil && mapPoint.ObjectId != objectId {
		return nil, fmt.Errorf("map point with this objectId already exist")
	}
	if _, err := s.repo.FindAreaByObjectId(objectId); err == nil && mapPoint.ObjectId != objectId {
		return nil, fmt.Errorf("area with this objectId already exist")
	}

	geom, err := s.repo.GetGeomFromPoint(x, y)
	if err != nil {
		return nil, err
	}

	mapPoint.ObjectId = objectId
	mapPoint.Geom = *geom

	mapPoint, err = s.repo.UpdateMapPoint(mapPoint)
	if err != nil {
		return nil, err
	}

	point, err := s.repo.GetPoint(mapPoint.Geom)
	if err != nil {
		return nil, err
	}

	return &MapPointOutput{
		Id:       mapPoint.ID,
		X:        point.X,
		Y:        point.Y,
		MapId:    mapPoint.MapId,
		ObjectId: mapPoint.ObjectId,
		NodeId:   mapPoint.Node.ID,
	}, nil
}

func (s *Service) DeleteMapPoint(mapPointId uint) error {
	return s.repo.DeleteMapPoint(mapPointId)
}

// Terminal

func (s *Service) GetTerminals() []TerminalOutput {
	terminals := s.repo.GetTerminals()
	output := make([]TerminalOutput, 0)
	for _, terminal := range terminals {
		point, err := s.repo.GetPoint(terminal.Geom)
		if err != nil {
			logrus.Info(err)
		}

		personPoint, err := s.repo.GetPoint(terminal.PersonPoint)
		if err != nil {
			logrus.Info(err)
		}

		terminalOutput := TerminalOutput{
			Id:          terminal.ID,
			X:           point.X,
			Y:           point.Y,
			PersonPoint: personPoint,
			MapId:       terminal.MapId,
			TerminalId:  terminal.TerminalId,
		}

		output = append(output, terminalOutput)
	}

	return output
}

func (s *Service) CreateTerminal(x, y float64, personPoint Point, mapId, terminalId string) (*TerminalOutput, error) {
	if _, err := s.repo.FindTerminalByTerminalId(terminalId); err == nil {
		return nil, fmt.Errorf("terminal with this terminalId already exist")
	}
	geom, err := s.repo.GetGeomFromPoint(x, y)
	if err != nil {
		return nil, err
	}

	personPointGeom, err := s.repo.GetGeomFromPoint(personPoint.X, personPoint.Y)
	if personPointGeom == nil {
		return nil, err
	}

	terminal := &Terminal{
		Geom:        *geom,
		MapId:       mapId,
		PersonPoint: *personPointGeom,
		TerminalId:  terminalId,
	}

	terminal, err = s.repo.CreateTerminal(terminal)
	if err != nil {
		return nil, err
	}

	point, err := s.repo.GetPoint(terminal.Geom)
	if err != nil {
		return nil, err
	}

	personPoint, err = s.repo.GetPoint(terminal.Geom)
	if err != nil {
		return nil, err
	}

	return &TerminalOutput{
		Id:          terminal.ID,
		X:           point.X,
		Y:           point.Y,
		MapId:       terminal.MapId,
		TerminalId:  terminal.TerminalId,
		PersonPoint: personPoint,
	}, nil
}

func (s *Service) UpdateTerminal(x, y float64, personPoint Point, terminalId string, id uint) (*TerminalOutput, error) {
	terminal, err := s.repo.FindTerminalById(id)
	if err != nil {
		return nil, err
	}

	if _, err = s.repo.FindTerminalByTerminalId(terminalId); err == nil && terminal.TerminalId != terminalId {
		return nil, fmt.Errorf("terminal with this terminalId already exist")
	}

	geom, err := s.repo.GetGeomFromPoint(x, y)
	if err != nil {
		return nil, err
	}

	personPointGeom, err := s.repo.GetGeomFromPoint(personPoint.X, personPoint.Y)
	if personPointGeom == nil {
		return nil, err
	}

	terminal.TerminalId = terminalId
	terminal.PersonPoint = *personPointGeom
	terminal.Geom = *geom

	terminal, err = s.repo.UpdateTerminal(terminal)
	if err != nil {
		return nil, err
	}

	point, err := s.repo.GetPoint(terminal.Geom)
	if err != nil {
		return nil, err
	}

	personPoint, err = s.repo.GetPoint(terminal.Geom)
	if err != nil {
		return nil, err
	}

	return &TerminalOutput{
		Id:          terminal.ID,
		X:           point.X,
		Y:           point.Y,
		MapId:       terminal.MapId,
		TerminalId:  terminal.TerminalId,
		PersonPoint: personPoint,
	}, nil
}

func (s *Service) DeleteTerminal(terminalId uint) error {
	return s.repo.DeleteTerminal(terminalId)
}

// Other

func (s *Service) FindShortestPath(startX, startY *float64, nodeId uint, mapID string, terminalId *uint) ([]ShortestPathPoint, error) {
	var srcCost *float64
	var trgCost *float64
	var points []ShortestPathPoint
	var terminal *Terminal
	var err error

	if terminalId != nil {
		terminal, err = s.repo.FindTerminalById(*terminalId)
		if err != nil {
			return nil, err
		}

		terminalPoint, err := s.repo.GetPoint(terminal.Geom)
		if err != nil {
			return nil, err
		}
		startX = &terminalPoint.X
		startY = &terminalPoint.Y
	}

	if startX == nil && startY == nil {
		return nil, fmt.Errorf("no need to find shortest path")
	}

	points = append(points, ShortestPathPoint{X: *startX, Y: *startY, Direction: "straight"})

	targetNode, err := s.repo.FindNodeById(nodeId)
	if err != nil {
		return nil, err
	}

	startPoint, err := s.repo.GetPointFromXAndY(*startX, *startY)
	if err != nil {
		return nil, err
	}

	nearestRoad, err := s.repo.GetNearestRoad(*startX, *startY, mapID)
	if err != nil {
		return nil, err
	}

	closestPoint, err := s.repo.GetClosestPoint(nearestRoad.Geom, startPoint)
	if err != nil {
		return nil, err
	}

	sourceDijkstra, err := s.repo.GetDijkstra(*nearestRoad.SourceId, targetNode.ID)
	if err != nil {
		return nil, err
	}

	targetDijkstra, err := s.repo.GetDijkstra(*nearestRoad.TargetId, targetNode.ID)
	if err != nil {
		return nil, err
	}

	if len(sourceDijkstra) > 0 {
		srcCost = &sourceDijkstra[len(sourceDijkstra)-1].AggCost
	}
	if len(targetDijkstra) > 0 {
		trgCost = &targetDijkstra[len(targetDijkstra)-1].AggCost
	}

	if comparePointers(srcCost, trgCost) <= 0 {
		for _, sr := range sourceDijkstra {
			point, err := s.repo.GetPointFromNodeId(sr.Node)
			if err != nil {
				return nil, err
			}
			if closestPoint.X == point.X && closestPoint.Y == point.Y {
				continue
			}
			points = append(points, point)
		}
	} else {
		for _, tr := range targetDijkstra {
			point, err := s.repo.GetPointFromNodeId(tr.Node)
			if err != nil {
				return nil, err
			}
			if closestPoint.X == point.X && closestPoint.Y == point.Y {
				continue
			}
			points = append(points, point)
		}
	}

	nodePoint, err := s.repo.GetPoint(targetNode.TheGeom)
	if err != nil {
		return nil, err
	}

	points = append(points, ShortestPathPoint{X: nodePoint.X, Y: nodePoint.Y})

	for i := 0; i < len(points)-2; i++ {
		if terminal != nil && i == 0 {
			terminalPersonPoint, err := s.repo.GetPoint(terminal.PersonPoint)
			if err != nil {
				return nil, err
			}
			azimuth, err := s.repo.GetAzimuthFromPoints(
				ShortestPathPoint{
					X: terminalPersonPoint.X,
					Y: terminalPersonPoint.Y,
				},
				points[i],
				points[i],
				points[i+1],
			)
			if err != nil {
				return nil, err
			}
			direction := s.GetDirection(azimuth.Azimuth, azimuth.Heading)
			points[i].Direction = direction
		}
		azimuth, err := s.repo.GetAzimuthFromPoints(points[i], points[i+1], points[i+1], points[i+2])
		if err != nil {
			return nil, err
		}
		direction := s.GetDirection(azimuth.Azimuth, azimuth.Heading)
		points[i+1].Direction = direction
	}

	if len(points) >= 2 {
		i := len(points) - 2
		azimuth, err := s.repo.GetAzimuthFromPoints(points[i], points[i+1], points[i], points[i+1])
		if err != nil {
			return nil, err
		}
		direction := s.GetDirection(azimuth.Azimuth, azimuth.Heading)
		points[i+1].Direction = direction
	}
	points[len(points)-1].Direction = "straight"

	return points, nil
}

func (s *Service) GetDirection(azimuth, heading float64) string {
	angleDiffRad := math.Atan2(math.Sin(azimuth-heading), math.Cos(azimuth-heading))
	angleDiffDeg := angleDiffRad * 180 / math.Pi
	absAngleDiffDeg := math.Abs(angleDiffDeg)

	if absAngleDiffDeg <= 10 {
		return "straight"
	} else if absAngleDiffDeg >= 170 {
		return "back"
	} else if angleDiffDeg > 0 {
		return "right"
	} else if angleDiffDeg < 0 && absAngleDiffDeg < 170 {
		return "left"
	}
	return "undefined"
}

func comparePointers(a, b *float64) int {
	if a == nil && b == nil {
		return 0
	}
	if a == nil {
		return -1
	}
	if b == nil {
		return 1
	}
	if *a < *b {
		return -1
	}
	if *a > *b {
		return 1
	}
	return 0
}

func SendPOISetRequest(area *Area, mapPoint *MapPoint, objectId string) error {
	var data map[string]interface{}

	if area != nil {
		cords, err := ParseWkbPolygon(area.Geom)
		if err != nil {
			return err
		}
		data = map[string]interface{}{
			"area": map[string]interface{}{
				"cords":  cords,
				"nodeId": area.Node.ID,
				"areaId": area.ID,
				"type":   "area",
			},
			"point":   nil,
			"mapUlid": area.MapId,
		}
		objectId = area.ObjectId
	} else if mapPoint != nil {
		cords, err := ParseWkbPoint(mapPoint.Geom)
		if err != nil {
			return err
		}
		data = map[string]interface{}{
			"area": nil,
			"point": map[string]interface{}{
				"cords":   cords,
				"nodeId":  mapPoint.Node.ID,
				"pointId": mapPoint.ID,
				"type":    "point",
			},
			"mapUlid": mapPoint.MapId,
		}
		objectId = mapPoint.ObjectId
	} else {
		data = nil
	}

	baseUrl := viper.GetString("admin_base_url")
	url := fmt.Sprintf("%s/api/points-of-interest/%s/navi", baseUrl, objectId)

	jsonData, err := json.Marshal(data)
	if err != nil {
		return fmt.Errorf("failed to marshal JSON: %w", err)
	}

	req, err := http.NewRequest("POST", url, bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("failed to create request: %w", err)
	}

	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{}

	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf("failed to send request: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("unexpected status code: %d", resp.StatusCode)
	}

	return nil
}

func SendTerminalSetRequest(terminal *Terminal, terminalId string) error {
	var data map[string]interface{}

	if terminal != nil {
		cords, err := ParseWkbPoint(terminal.Geom)
		if err != nil {
			return err
		}
		personCords, err := ParseWkbPoint(terminal.PersonPoint)
		if err != nil {
			return err
		}
		data = map[string]interface{}{
			"point": map[string]interface{}{
				"cords":       cords,
				"personCords": personCords,
				"terminalId":  terminal.ID,
			},
			"mapUlid": terminal.MapId,
		}
	} else {
		data = nil
	}

	baseUrl := viper.GetString("admin_base_url")
	url := fmt.Sprintf("%s/api/terminals/%s/navi", baseUrl, terminalId)

	jsonData, err := json.Marshal(data)
	if err != nil {
		return fmt.Errorf("failed to marshal JSON: %w", err)
	}

	req, err := http.NewRequest("POST", url, bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("failed to create request: %w", err)
	}

	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{}

	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf("failed to send request: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("unexpected status code: %d", resp.StatusCode)
	}

	return nil
}
