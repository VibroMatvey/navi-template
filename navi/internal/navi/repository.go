package navi

import (
	"errors"
	"fmt"
	"gorm.io/gorm"
)

type Repository struct {
	db *gorm.DB
}

func NewRepository(db *gorm.DB) *Repository {
	return &Repository{db: db}
}

// Road

func (r *Repository) CreateRoad(model *Road) (*Road, error) {
	var road *Road

	err := r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Create(model).Error; err != nil {
			return err
		}

		qTopology := `SELECT pgr_createTopology('roads', 0.001, 'geom', 'id', 'source', 'target');`
		if err := tx.Exec(qTopology).Error; err != nil {
			return err
		}

		qUpdateCost := `UPDATE roads SET cost = ST_Length(geom) WHERE id = ?;`
		if err := tx.Exec(qUpdateCost, model.ID).Error; err != nil {
			return err
		}

		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Preload("Source").Preload("Target").Where("id = ?", model.ID).Find(&road)

	return road, nil
}

func (r *Repository) UpdateRoad(model *Road) (*Road, error) {
	err := r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Save(&model).Error; err != nil {
			return err
		}

		qTopology := `SELECT pgr_createTopology('roads', 0.001, 'geom', 'id', 'source', 'target');`
		if err := tx.Exec(qTopology).Error; err != nil {
			return err
		}

		qUpdateCost := `UPDATE roads SET cost = ST_Length(geom) WHERE id = ?;`
		if err := tx.Exec(qUpdateCost, model.ID).Error; err != nil {
			return err
		}

		return nil
	})

	r.db.Preload("Source").Preload("Target").Where("id = ?", model.ID).Find(&model)

	if err != nil {
		return nil, err
	}

	return model, nil
}

func (r *Repository) GetRoads(mapId string) []Road {
	var roads []Road

	ctx := r.db.Preload("Source").Preload("Target")

	if mapId != "" {
		ctx.Where("map_id = ?", mapId)
	}

	ctx.Find(&roads)

	return roads
}

func (r *Repository) FindRoadById(roadId uint) (*Road, error) {
	var record *Road

	err := r.db.Where("id = ?", roadId).First(&record).Error

	return record, err
}

func (r *Repository) DeleteRoad(roadId uint) error {
	err := r.db.Transaction(func(tx *gorm.DB) error {
		var road Road
		if err := tx.
			Preload("Source").
			Preload("Source.Area").
			Preload("Source.MapPoint").
			Preload("Target").
			Preload("Target.Area").
			Preload("Target.MapPoint").
			Where("id = ?", roadId).
			First(&road).Error; err != nil {
			return err
		}

		if err := tx.Delete(&Road{}, "id = ?", roadId).Error; err != nil {
			return err
		}

		var sourceRoad Road
		err := tx.Where("source = ? OR target = ? AND id != ?", *road.SourceId, *road.SourceId, roadId).First(&sourceRoad).Error
		if errors.Is(err, gorm.ErrRecordNotFound) {
			if err := tx.Delete(&Node{}, "id = ?", road.Source.ID).Error; err != nil {
				return err
			}
			if road.Source.MapPointId != nil {
				if err := tx.Delete(&MapPoint{}, "id = ?", *road.Source.MapPointId).Error; err != nil {
					return err
				}
			}
			if road.Source.AreaId != nil {
				if err := tx.Delete(&Area{}, "id = ?", *road.Source.AreaId).Error; err != nil {
					return err
				}
			}
			if road.Source.MapPoint != nil && road.Source.MapPoint.ObjectId != "" {
				if err := SendPOISetRequest(nil, nil, road.Source.MapPoint.ObjectId); err != nil {
					return err
				}
			}
			if road.Source.Area != nil && road.Source.Area.ObjectId != "" {
				if err := SendPOISetRequest(nil, nil, road.Source.Area.ObjectId); err != nil {
					return err
				}
			}
		} else if err != nil {
			return err
		}

		var targetRoad Road
		err = tx.Where("source = ? OR target = ? AND id != ?", *road.TargetId, *road.TargetId, roadId).First(&targetRoad).Error
		if errors.Is(err, gorm.ErrRecordNotFound) {
			if err := tx.Delete(&Node{}, "id = ?", road.Target.ID).Error; err != nil {
				return err
			}
			if road.Target.MapPointId != nil {
				if err := tx.Delete(&MapPoint{}, "id = ?", *road.Target.MapPointId).Error; err != nil {
					return err
				}
			}
			if road.Target.AreaId != nil {
				if err := tx.Delete(&Area{}, "id = ?", *road.Target.AreaId).Error; err != nil {
					return err
				}
			}
			if road.Target.MapPoint != nil && road.Target.MapPoint.ObjectId != "" {
				if err := SendPOISetRequest(nil, nil, road.Target.MapPoint.ObjectId); err != nil {
					return err
				}
			}
			if road.Target.Area != nil && road.Target.Area.ObjectId != "" {
				if err := SendPOISetRequest(nil, nil, road.Target.Area.ObjectId); err != nil {
					return err
				}
			}
		} else if err != nil {
			return err
		}

		return nil
	})

	if err != nil {
		return err
	}

	return nil
}

// Area

func (r *Repository) CreateArea(model *Area, nodeId uint) (*Area, error) {
	err := r.db.Transaction(func(tx *gorm.DB) error {
		var node Node
		if err := r.db.Where("id = ?", nodeId).First(&node).Error; err != nil {
			return err
		}
		if node.MapPointId != nil || node.AreaId != nil {
			return fmt.Errorf("area or map point already exists for this node")
		}
		if err := tx.Create(model).Error; err != nil {
			return err
		}
		node.AreaId = &model.ID
		if err := tx.Save(&node).Error; err != nil {
			return err
		}
		model.Node = &node
		if err := tx.Where("id = ?", model.ID).First(&model).Error; err != nil {
			return err
		}
		if err := SendPOISetRequest(model, nil, model.ObjectId); err != nil {
			return err
		}
		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Preload("Node").Where("id = ?", model.ID).Find(&model)

	return model, nil
}

func (r *Repository) UpdateArea(model *Area) (*Area, error) {
	var oldArea Area
	err := r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Where("id = ?", model.ID).First(&oldArea).Error; err != nil {
			return err
		}
		if err := tx.Save(model).Error; err != nil {
			return err
		}
		if err := SendPOISetRequest(model, nil, model.ObjectId); err != nil {
			return err
		}
		if oldArea.ObjectId != model.ObjectId {
			if err := SendPOISetRequest(nil, nil, oldArea.ObjectId); err != nil {
				return err
			}
		}

		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Preload("Node").Where("id = ?", model.ID).Find(&model)

	return model, nil
}

func (r *Repository) GetAreas() []Area {
	var records []Area

	r.db.Preload("Node").Find(&records)

	return records
}

func (r *Repository) FindAreaById(areaId uint) (*Area, error) {
	var record Area

	err := r.db.Preload("Node").Where("id = ?", areaId).First(&record).Error

	return &record, err
}

func (r *Repository) DeleteArea(areaId uint) error {
	err := r.db.Transaction(func(tx *gorm.DB) error {
		area, err := r.FindAreaById(areaId)
		if err != nil {
			return err
		}

		var nodes []Node
		if err := tx.Where("area_id = ?", areaId).Find(&nodes).Error; err != nil {
			return err
		}

		for _, node := range nodes {
			node.AreaId = nil
			if err := tx.Save(&node).Error; err != nil {
				return err
			}
		}

		if err := tx.Delete(&Area{}, "id = ?", areaId).Error; err != nil {
			return err
		}
		if err := SendPOISetRequest(nil, nil, area.ObjectId); err != nil {
			return err
		}

		return nil
	})

	if err != nil {
		return err
	}

	return nil
}

func (r *Repository) FindAreaByObjectId(objectId string) (*Area, error) {
	var record *Area
	err := r.db.Preload("Node").Where("object_id = ?", objectId).First(&record).Error
	if errors.Is(err, gorm.ErrRecordNotFound) {
		return nil, fmt.Errorf("area with objectId %s not found", objectId)
	}
	return record, err
}

// MapPoint

func (r *Repository) CreateMapPoint(model *MapPoint, nodeId uint) (*MapPoint, error) {
	var mapPoint MapPoint

	err := r.db.Transaction(func(tx *gorm.DB) error {
		var node Node
		if err := r.db.Where("id = ?", nodeId).First(&node).Error; err != nil {
			return err
		}
		if node.MapPointId != nil || node.AreaId != nil {
			return fmt.Errorf("area or map point already exists for this node")
		}
		if err := tx.Create(model).Error; err != nil {
			return err
		}
		node.MapPointId = &model.ID
		if err := tx.Save(&node).Error; err != nil {
			return err
		}
		model.Node = &node
		if err := SendPOISetRequest(nil, model, model.ObjectId); err != nil {
			return err
		}

		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Preload("Node").Where("id = ?", model.ID).Find(&mapPoint)

	return &mapPoint, nil
}

func (r *Repository) UpdateMapPoint(model *MapPoint) (*MapPoint, error) {
	var mapPoint MapPoint

	err := r.db.Transaction(func(tx *gorm.DB) error {
		var oldMapPoint MapPoint
		if err := tx.Where("id = ?", model.ID).First(&oldMapPoint).Error; err != nil {
			return err
		}
		if err := tx.Save(model).Error; err != nil {
			return err
		}
		if err := SendPOISetRequest(nil, model, model.ObjectId); err != nil {
			return err
		}
		if oldMapPoint.ObjectId != model.ObjectId {
			if err := SendPOISetRequest(nil, nil, oldMapPoint.ObjectId); err != nil {
				return err
			}
		}
		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Preload("Node").Where("id = ?", model.ID).Find(&mapPoint)

	return &mapPoint, nil
}

func (r *Repository) GetMapPoints() []MapPoint {
	var records []MapPoint

	r.db.Preload("Node").Find(&records)

	return records
}

func (r *Repository) FindMapPointById(mapPointId uint) (*MapPoint, error) {
	var record MapPoint

	err := r.db.Preload("Node").Where("id = ?", mapPointId).First(&record).Error

	return &record, err
}

func (r *Repository) DeleteMapPoint(mapPointId uint) error {
	err := r.db.Transaction(func(tx *gorm.DB) error {
		mapPoint, err := r.FindMapPointById(mapPointId)
		if err != nil {
			return err
		}

		var nodes []Node
		if err := tx.Where("map_point_id = ?", mapPointId).Find(&nodes).Error; err != nil {
			return err
		}

		for _, node := range nodes {
			node.MapPointId = nil
			if err := tx.Save(&node).Error; err != nil {
				return err
			}
		}

		if err := tx.Delete(&MapPoint{}, "id = ?", mapPointId).Error; err != nil {
			return err
		}
		if err := SendPOISetRequest(nil, nil, mapPoint.ObjectId); err != nil {
			return err
		}

		return nil
	})

	if err != nil {
		return err
	}

	return nil
}

func (r *Repository) FindMapPointByObjectId(objectId string) (*MapPoint, error) {
	var record *MapPoint
	err := r.db.Preload("Node").Where("object_id = ?", objectId).First(&record).Error
	if errors.Is(err, gorm.ErrRecordNotFound) {
		return nil, fmt.Errorf("map point with objectId %s not found", objectId)
	}
	return record, err
}

// Node

func (r *Repository) FindNodeById(nodeId uint) (*Node, error) {
	var record Node

	err := r.db.Where("id = ?", nodeId).First(&record).Error

	return &record, err
}

// Terminal

func (r *Repository) CreateTerminal(model *Terminal) (*Terminal, error) {
	var record Terminal

	err := r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Create(model).Error; err != nil {
			return err
		}
		if err := SendTerminalSetRequest(model, model.TerminalId); err != nil {
			return err
		}
		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Where("id = ?", model.ID).Find(&record)

	return &record, nil
}

func (r *Repository) UpdateTerminal(model *Terminal) (*Terminal, error) {
	var record Terminal

	err := r.db.Transaction(func(tx *gorm.DB) error {
		var oldTerminal Terminal
		if err := tx.Where("id = ?", model.ID).First(&oldTerminal).Error; err != nil {
			return err
		}
		if err := tx.Save(model).Error; err != nil {
			return err
		}
		if err := SendTerminalSetRequest(model, model.TerminalId); err != nil {
			return err
		}
		if oldTerminal.TerminalId != model.TerminalId {
			if err := SendTerminalSetRequest(nil, oldTerminal.TerminalId); err != nil {
				return err
			}
		}
		return nil
	})

	if err != nil {
		return nil, err
	}

	r.db.Where("id = ?", model.ID).Find(&record)

	return &record, nil
}

func (r *Repository) GetTerminals() []Terminal {
	var records []Terminal

	r.db.Find(&records)

	return records
}

func (r *Repository) FindTerminalById(terminalId uint) (*Terminal, error) {
	var record *Terminal

	err := r.db.Where("id = ?", terminalId).First(&record).Error

	return record, err
}

func (r *Repository) DeleteTerminal(terminalId uint) error {
	err := r.db.Transaction(func(tx *gorm.DB) error {
		oldTerminal, err := r.FindTerminalById(terminalId)
		if err != nil {
			return err
		}
		if err = tx.Delete(&Terminal{}, "id = ?", terminalId).Error; err != nil {
			return err
		}
		if err = SendTerminalSetRequest(nil, oldTerminal.TerminalId); err != nil {
			return err
		}

		return nil
	})

	return err
}

func (r *Repository) FindTerminalByTerminalId(terminalId string) (*Terminal, error) {
	var record *Terminal
	err := r.db.Where("terminal_id = ?", terminalId).First(&record).Error
	if errors.Is(err, gorm.ErrRecordNotFound) {
		return nil, fmt.Errorf("terminal with areaId %s not found", terminalId)
	}
	return record, err
}

// Other

func (r *Repository) GetPointFromXAndY(x, y float64) (string, error) {
	var point string

	q := `SELECT ST_SetSRID(ST_Point(?,?), 3857)`
	err := r.db.Raw(q, x, y).Scan(&point).Error

	return point, err
}

func (r *Repository) GetPoint(geom string) (Point, error) {
	var point Point

	q := `SELECT ST_X(?) as x, ST_Y(?) as y`
	err := r.db.Raw(q, geom, geom).Scan(&point).Error

	return point, err
}

func (r *Repository) GetNearestRoad(x, y float64, mapID string) (*Road, error) {
	var road *Road

	q := `SELECT
            r.geom,
            r.source,
            r.target,
            ST_Distance(ST_SetSRID(ST_Point(?,?), 3857), r.geom) AS distance
        FROM roads r
        WHERE r.map_id = ?
        ORDER BY distance
        LIMIT 1`

	err := r.db.Raw(q, x, y, mapID).Scan(&road).Error

	if road == nil {
		return nil, fmt.Errorf("road not found")
	}

	return road, err
}

func (r *Repository) GetClosestPoint(from, to string) (ShortestPathPoint, error) {
	var res ShortestPathPoint

	q := `SELECT
        ST_X(ST_ClosestPoint(?, ?)) AS x,
        ST_Y(ST_ClosestPoint(?, ?)) AS y
	`
	err := r.db.Raw(q, from, to, from, to).Scan(&res).Error

	return res, err
}

func (r *Repository) GetDijkstra(from uint, to uint) ([]DijkstraRes, error) {
	var res []DijkstraRes

	q := `SELECT d.node, d.agg_cost
        FROM pgr_dijkstra(
            'SELECT id, source, target, cost, cost AS reverse_cost FROM roads',
            $1::integer,
            $2::integer,
            directed := false
        ) d
        WHERE d.edge <> -1
        ORDER BY d.seq`

	err := r.db.Raw(q, from, to).Scan(&res).Error

	return res, err
}

func (r *Repository) GetPointFromNodeId(nodeId uint) (ShortestPathPoint, error) {
	var res ShortestPathPoint

	q := `SELECT
        ST_X(the_geom) AS x,
        ST_Y(the_geom) AS y
		FROM roads_vertices_pgr r WHERE r.id = ?
		LIMIT 1`

	err := r.db.Raw(q, nodeId).Scan(&res).Error

	return res, err
}

func (r *Repository) GetGeomFromPoint(x, y float64) (*string, error) {
	var res string

	q := `SELECT ST_SetSRID(ST_MakePoint(?, ?), 3857) AS geom;`

	err := r.db.Raw(q, x, y).Scan(&res).Error

	return &res, err
}

func (r *Repository) GetAzimuthFromPoints(source, target, nextSource, nextTarget ShortestPathPoint) (*AzimuthOutput, error) {
	var res AzimuthOutput

	q := `
	SELECT
             ST_Azimuth(ST_Point(?, ?), ST_Point(?, ?)) AS azimuth,
             ST_Azimuth(ST_Point(?, ?), ST_Point(?, ?)) AS heading
	`

	err := r.db.Raw(q, nextSource.X, nextSource.Y, nextTarget.X, nextTarget.Y, source.X, source.Y, target.X, target.Y).Scan(&res).Error

	return &res, err
}
