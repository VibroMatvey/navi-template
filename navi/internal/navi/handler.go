package navi

import (
	"github.com/VibroMatvey/postgis-go-navi/pkg/response"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
)

type Handler struct {
	service *Service
}

func NewHandler(service *Service) *Handler {
	return &Handler{
		service: service,
	}
}

// Roads endpoints

// CreateRoad Create new road
// @Summary Create new road
// @Schemes
// @Tags Road
// @Accept json
// @Produce json
// @Param request body RoadInput true "Road input data"
// @Success 200 {object} RoadOutput
// @Router /roads [post]
func (h *Handler) CreateRoad(c *gin.Context) {
	var input RoadInput

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	roadOutput, err := h.service.CreateRoad(
		input.Start,
		input.End,
		input.MapId,
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &roadOutput)
}

// UpdateRoad Update exist road
// @Summary Update exist road
// @Schemes
// @Tags Road
// @Accept json
// @Produce json
// @Param id path int true "Road ID"
// @Param request body RoadEditInput true "Road input data"
// @Success 200 {object} RoadOutput
// @Router /roads/{id} [put]
func (h *Handler) UpdateRoad(c *gin.Context) {
	var input RoadEditInput
	id, _ := strconv.Atoi(c.Param("id"))

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	roadOutput, err := h.service.UpdateRoad(
		input.Start,
		input.End,
		uint(id),
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, roadOutput)
}

// DeleteRoad remove one road by id
// @Summary Remove one road by id
// @Schemes
// @Tags Road
// @Accept json
// @Produce json
// @Param id path int true "Road ID"
// @Success 204 "No Content"
// @Router /roads/{id} [delete]
func (h *Handler) DeleteRoad(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))
	err := h.service.DeleteRoad(uint(id))
	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}
	c.JSON(http.StatusNoContent, nil)
}

// FindShortestPath Find the shortest path
// @Summary Find the shortest path
// @Schemes
// @Tags Road
// @Accept json
// @Produce json
// @Param request body ShortestPathInput true "Road input data"
// @Success 200 {array} Point[]
// @Router /roads/shortest-path [post]
func (h *Handler) FindShortestPath(c *gin.Context) {
	var x *float64
	var y *float64
	var input ShortestPathInput

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	if input.Start == nil {
		x = nil
		y = nil
	} else {
		x = &input.Start.X
		y = &input.Start.Y
	}

	//utils.GenerateMetersFromLatLon(input.Start.X, input.Start.Y)
	nodes, err := h.service.FindShortestPath(
		x,
		y,
		input.NodeId,
		input.MapId,
		input.TerminalId,
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, nodes)
}

// FindRoads Find all roads
// @Summary Find all roads
// @Schemes
// @Tags Road
// @Accept json
// @Produce json
// @Param mapId query string false "Map ID to filter roads"
// @Success 200 {array} RoadOutput
// @Router /roads [get]
func (h *Handler) FindRoads(c *gin.Context) {
	roads := h.service.GetRoads(c.Query("mapId"))

	c.JSON(http.StatusOK, roads)
}

// Area endpoints

// CreateArea Create new area
// @Summary Create new area
// @Schemes
// @Tags Area
// @Accept json
// @Produce json
// @Param request body AreaInput true "Area input data"
// @Success 200 {object} AreaOutput
// @Router /areas [post]
func (h *Handler) CreateArea(c *gin.Context) {
	var input AreaInput

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	areaOutput, err := h.service.CreateArea(
		input.Cords,
		input.ObjectId,
		input.MapId,
		input.NodeId,
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &areaOutput)
}

// UpdateArea Update exist area
// @Summary Update exist area
// @Schemes
// @Tags Area
// @Accept json
// @Produce json
// @Param id path int true "Area ID"
// @Param request body AreaEditInput true "Area input data"
// @Success 200 {object} AreaOutput
// @Router /areas/{id} [put]
func (h *Handler) UpdateArea(c *gin.Context) {
	var input AreaEditInput
	id, _ := strconv.Atoi(c.Param("id"))

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	areaOutput, err := h.service.UpdateArea(
		input.Cords,
		input.ObjectId,
		uint(id),
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &areaOutput)
}

// DeleteArea remove one area by id
// @Summary Remove one area by id
// @Schemes
// @Tags Area
// @Accept json
// @Produce json
// @Param id path int true "Area ID"
// @Success 204 "No Content"
// @Router /areas/{id} [delete]
func (h *Handler) DeleteArea(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))
	err := h.service.DeleteArea(uint(id))
	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}
	c.JSON(http.StatusNoContent, nil)
}

// FindAreas Find all areas
// @Summary Find all areas
// @Schemes
// @Tags Area
// @Accept json
// @Produce json
// @Success 200 {array} AreaOutput
// @Router /areas [get]
func (h *Handler) FindAreas(c *gin.Context) {
	areas := h.service.GetAreas()

	c.JSON(http.StatusOK, areas)
}

// MapPoint endpoints

// CreateMapPoint Create new MapPoint
// @Summary Create new MapPoint
// @Schemes
// @Tags MapPoint
// @Accept json
// @Produce json
// @Param request body MapPointInput true "MapPoint input data"
// @Success 200 {object} MapPointOutput
// @Router /points [post]
func (h *Handler) CreateMapPoint(c *gin.Context) {
	var input MapPointInput

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	mapPointOutput, err := h.service.CreateMapPoint(
		input.X,
		input.Y,
		input.MapId,
		input.ObjectId,
		input.NodeId,
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &mapPointOutput)
}

// UpdateMapPoint Update exist MapPoint
// @Summary Update exist MapPoint
// @Schemes
// @Tags MapPoint
// @Accept json
// @Produce json
// @Param id path int true "MapPoint ID"
// @Param request body MapPointEditInput true "MapPoint input data"
// @Success 200 {object} MapPointOutput
// @Router /points/{id} [put]
func (h *Handler) UpdateMapPoint(c *gin.Context) {
	var input MapPointEditInput
	id, _ := strconv.Atoi(c.Param("id"))

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	mapPointOutput, err := h.service.UpdateMapPoint(
		input.X,
		input.Y,
		input.ObjectId,
		uint(id),
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &mapPointOutput)
}

// DeleteMapPoint remove one MapPoint by id
// @Summary Remove one MapPoint by id
// @Schemes
// @Tags MapPoint
// @Accept json
// @Produce json
// @Param id path int true "MapPoint ID"
// @Success 204 "No Content"
// @Router /points/{id} [delete]
func (h *Handler) DeleteMapPoint(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))
	err := h.service.DeleteMapPoint(uint(id))
	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}
	c.JSON(http.StatusNoContent, nil)
}

// FindMapPoints Find all MapPoints
// @Summary Find all MapPoints
// @Schemes
// @Tags MapPoint
// @Accept json
// @Produce json
// @Success 200 {array} MapPointOutput
// @Router /points [get]
func (h *Handler) FindMapPoints(c *gin.Context) {
	mapPoints := h.service.GetMapPoints()

	c.JSON(http.StatusOK, mapPoints)
}

// MapPoint endpoints

// CreateTerminal Create new Terminal
// @Summary Create new Terminal
// @Schemes
// @Tags Terminal
// @Accept json
// @Produce json
// @Param request body TerminalInput true "MapPoint input data"
// @Success 200 {object} TerminalOutput
// @Router /terminals [post]
func (h *Handler) CreateTerminal(c *gin.Context) {
	var input TerminalInput

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	terminalOutput, err := h.service.CreateTerminal(
		input.X,
		input.Y,
		input.PersonPoint,
		input.MapId,
		input.TerminalId,
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &terminalOutput)
}

// UpdateTerminal Update exist Terminal
// @Summary Update exist Terminal
// @Schemes
// @Tags Terminal
// @Accept json
// @Produce json
// @Param id path int true "Terminal ID"
// @Param request body TerminalEditInput true "Terminal input data"
// @Success 200 {object} TerminalOutput
// @Router /terminals/{id} [put]
func (h *Handler) UpdateTerminal(c *gin.Context) {
	var input TerminalEditInput
	id, _ := strconv.Atoi(c.Param("id"))

	if err := c.ShouldBindJSON(&input); err != nil {
		response.NewErrorResponse(c, http.StatusBadRequest, err.Error(), 0)
		return
	}

	terminalOutput, err := h.service.UpdateTerminal(
		input.X,
		input.Y,
		input.PersonPoint,
		input.TerminalId,
		uint(id),
	)

	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}

	c.JSON(http.StatusOK, &terminalOutput)
}

// DeleteTerminal remove one Terminal by id
// @Summary Remove one Terminal by id
// @Schemes
// @Tags Terminal
// @Accept json
// @Produce json
// @Param id path int true "Terminal ID"
// @Success 204 "No Content"
// @Router /terminals/{id} [delete]
func (h *Handler) DeleteTerminal(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))
	err := h.service.DeleteTerminal(uint(id))
	if err != nil {
		response.NewErrorResponse(c, http.StatusInternalServerError, err.Error(), 0)
		return
	}
	c.JSON(http.StatusNoContent, nil)
}

// FindTerminals Find all Terminals
// @Summary Find all Terminals
// @Schemes
// @Tags Terminal
// @Accept json
// @Produce json
// @Success 200 {array} TerminalOutput
// @Router /terminals [get]
func (h *Handler) FindTerminals(c *gin.Context) {
	terminals := h.service.GetTerminals()

	c.JSON(http.StatusOK, terminals)
}
