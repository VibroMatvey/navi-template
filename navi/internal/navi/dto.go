package navi

type Point struct {
	X float64 `json:"x" binding:"required"`
	Y float64 `json:"y" binding:"required"`
}

type ShortestPathPoint struct {
	X         float64 `json:"x"`
	Y         float64 `json:"y"`
	Direction string  `json:"direction"`
}

type RoadOutputPoint struct {
	NodeId uint    `json:"nodeId"`
	X      float64 `json:"x" binding:"required"`
	Y      float64 `json:"y" binding:"required"`
}

type GlobalPoint struct {
	scrX, scrY, lat, lng float64
	pos                  GlobalXY
}

type GlobalXY struct {
	x, y float64
}

type RoadInput struct {
	Start Point  `json:"source" binding:"required"`
	End   Point  `json:"target" binding:"required"`
	MapId string `json:"mapId" binding:"required"`
}

type RoadEditInput struct {
	Start Point `json:"source" binding:"required"`
	End   Point `json:"target" binding:"required"`
}

type RoadOutput struct {
	Id     uint            `json:"id"`
	Source RoadOutputPoint `json:"source"`
	Target RoadOutputPoint `json:"target"`
	MapId  string          `json:"mapId"`
}

type ShortestPathInput struct {
	Start      *Point `json:"start"`
	NodeId     uint   `json:"nodeId" binding:"required"`
	MapId      string `json:"mapId" binding:"required"`
	TerminalId *uint  `json:"terminalId"`
}

type DijkstraRes struct {
	Node    uint
	AggCost float64
}

type AreaInput struct {
	Cords    [][]Point `json:"cords" binding:"required"`
	ObjectId string    `json:"objectId" binding:"required"`
	MapId    string    `json:"mapId" binding:"required"`
	NodeId   uint      `json:"nodeId"`
}

type AreaEditInput struct {
	Cords    [][]Point `json:"cords" binding:"required"`
	ObjectId string    `json:"objectId" binding:"required"`
}

type AreaOutput struct {
	Id       uint      `json:"id"`
	Cords    [][]Point `json:"cords"`
	ObjectId string    `json:"objectId"`
	MapId    string    `json:"mapId"`
	NodeId   uint      `json:"nodeId"`
}

type MapPointInput struct {
	X        float64 `json:"x" binding:"required"`
	Y        float64 `json:"y" binding:"required"`
	ObjectId string  `json:"objectId" binding:"required"`
	MapId    string  `json:"mapId" binding:"required"`
	NodeId   uint    `json:"nodeId"`
}

type MapPointEditInput struct {
	X        float64 `json:"x" binding:"required"`
	Y        float64 `json:"y" binding:"required"`
	ObjectId string  `json:"objectId" binding:"required"`
}

type MapPointOutput struct {
	Id       uint    `json:"id"`
	X        float64 `json:"x" binding:"required"`
	Y        float64 `json:"y" binding:"required"`
	ObjectId string  `json:"objectId" binding:"required"`
	MapId    string  `json:"mapId" binding:"required"`
	NodeId   uint    `json:"nodeId"`
}

type TerminalInput struct {
	X           float64 `json:"x" binding:"required"`
	Y           float64 `json:"y" binding:"required"`
	PersonPoint Point   `json:"personPoint" binding:"required"`
	TerminalId  string  `json:"terminalId" binding:"required"`
	MapId       string  `json:"mapId" binding:"required"`
}

type TerminalEditInput struct {
	X           float64 `json:"x" binding:"required"`
	Y           float64 `json:"y" binding:"required"`
	PersonPoint Point   `json:"personPoint" binding:"required"`
	TerminalId  string  `json:"terminalId" binding:"required"`
}

type TerminalOutput struct {
	Id          uint    `json:"id"`
	X           float64 `json:"x" binding:"required"`
	Y           float64 `json:"y" binding:"required"`
	PersonPoint Point   `json:"personPoint" binding:"required"`
	TerminalId  string  `json:"terminalId" binding:"required"`
	MapId       string  `json:"mapId" binding:"required"`
}

type AzimuthOutput struct {
	Azimuth float64
	Heading float64
}
