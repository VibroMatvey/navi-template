package router

import (
	_ "github.com/VibroMatvey/postgis-go-navi/docs"
	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"
	swaggerfiles "github.com/swaggo/files"
	ginSwagger "github.com/swaggo/gin-swagger"
)

func InitRoutes() *gin.Engine {
	corsConfig := cors.DefaultConfig()
	corsConfig.AllowOrigins = []string{"*"}

	router := gin.New()
	router.Use(cors.New(corsConfig))

	router.GET("/docs/*any", func(c *gin.Context) {
		if c.Request.RequestURI == "/docs/" {
			c.Redirect(302, "/docs/index.html")
			return
		}
		ginSwagger.WrapHandler(swaggerfiles.Handler)(c)
	})

	return router
}
