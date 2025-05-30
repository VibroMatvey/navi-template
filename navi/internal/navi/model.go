package navi

type Road struct {
	ID       uint    `gorm:"primary_key" json:"id"`
	SourceId *uint   `gorm:"null;column:source;constraint:OnDelete:SET NULL"`
	TargetId *uint   `gorm:"null;column:target;constraint:OnDelete:SET NULL"`
	Source   *Node   `gorm:"foreignKey:SourceId;"`
	Target   *Node   `gorm:"foreignKey:TargetId;"`
	Cost     float64 `gorm:"null" default:"0" json:"-"`
	Geom     string  `gorm:"type:geometry(LineString, 3857)"`
	MapID    string  `gorm:"not null"`
}

type Node struct {
	ID         uint   `gorm:"primaryKey" json:"id"`
	TheGeom    string `gorm:"type:geometry(Point, 3857)"`
	Area       *Area  `gorm:"foreignKey:AreaId;"`
	AreaId     *uint
	MapPoint   *MapPoint `gorm:"foreignKey:MapPointId;"`
	MapPointId *uint
}

type Area struct {
	ID       uint   `gorm:"primary_key"`
	Geom     string `gorm:"type:geometry(Polygon, 3857)" gorm:"not null"`
	ObjectId string `gorm:"not null" json:"-"`
	MapId    string `gorm:"not null" json:"-"`
	Node     *Node  `gorm:"foreignKey:AreaId;constraint:OnUpdate:CASCADE,OnDelete:SET NULL;"`
}

type MapPoint struct {
	ID       uint   `gorm:"primary_key"`
	Geom     string `gorm:"type:geometry(Point, 3857)" gorm:"not null"`
	ObjectId string `gorm:"not null"`
	MapId    string `gorm:"not null"`
	Node     *Node  `gorm:"foreignKey:MapPointId;constraint:OnUpdate:CASCADE,OnDelete:SET NULL;"`
}

type Terminal struct {
	ID          uint   `gorm:"primary_key"`
	Geom        string `gorm:"type:geometry(Point, 3857)" gorm:"not null"`
	PersonPoint string `gorm:"type:geometry(Point, 3857)" gorm:"not null"`
	TerminalId  string `gorm:"not null"`
	MapId       string `gorm:"not null"`
}

func (Node) TableName() string {
	return "roads_vertices_pgr"
}
