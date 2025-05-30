package navi

import (
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

type Navi struct {
	repository *Repository
	service    *Service
	handler    *Handler
}

func NewNavi(db *gorm.DB, router *gin.Engine) {
	repository := NewRepository(db)
	service := NewService(repository)
	handler := NewHandler(service)

	v1 := router.Group("/api/v1")
	{
		roads := v1.Group("/roads")
		{
			roads.GET("", handler.FindRoads)
			roads.POST("", handler.CreateRoad)
			roads.PUT("/:id", handler.UpdateRoad)
			roads.DELETE("/:id", handler.DeleteRoad)
			roads.POST("/shortest-path", handler.FindShortestPath)
		}

		areas := v1.Group("/areas")
		{
			areas.GET("", handler.FindAreas)
			areas.POST("", handler.CreateArea)
			areas.PUT("/:id", handler.UpdateArea)
			areas.DELETE("/:id", handler.DeleteArea)
		}

		mapPoints := v1.Group("/points")
		{
			mapPoints.GET("", handler.FindMapPoints)
			mapPoints.POST("", handler.CreateMapPoint)
			mapPoints.PUT("/:id", handler.UpdateMapPoint)
			mapPoints.DELETE("/:id", handler.DeleteMapPoint)
		}

		terminals := v1.Group("/terminals")
		{
			terminals.GET("", handler.FindTerminals)
			terminals.POST("", handler.CreateTerminal)
			terminals.PUT("/:id", handler.UpdateTerminal)
			terminals.DELETE("/:id", handler.DeleteTerminal)
		}
	}
}
