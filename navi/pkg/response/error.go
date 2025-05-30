package response

import (
	"github.com/gin-gonic/gin"
	"github.com/sirupsen/logrus"
)

type errorResponse struct {
	Message string `json:"message"`
	Code    int    `json:"code"`
}

func NewErrorResponse(c *gin.Context, status int, message string, code int) {
	logrus.Error(message)
	c.AbortWithStatusJSON(status, errorResponse{message, code})
}
