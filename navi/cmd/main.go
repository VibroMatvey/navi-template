package main

import (
	"github.com/VibroMatvey/postgis-go-navi/internal/navi"
	"github.com/VibroMatvey/postgis-go-navi/pkg/database"
	"github.com/VibroMatvey/postgis-go-navi/pkg/router"
	"github.com/VibroMatvey/postgis-go-navi/pkg/server"
	"github.com/joho/godotenv"
	"github.com/sirupsen/logrus"
	"github.com/spf13/viper"
	"os"
)

// @title Navi API service
// @version 1.0
// @description Api service for found the shortest path.
// @host localhost:8000
// @BasePath /navi/api/v1
func main() {
	if err := initConfig(); err != nil {
		logrus.Fatalf("Error reading config file, %s", err)
	}

	if err := godotenv.Load(); err != nil {
		logrus.Fatalf("Error loading .env file, %s", err)
	}

	db, err := database.NewMysqlDB(database.Config{
		Dsn: os.Getenv("DATABASE_URL"),
	})

	if err != nil {
		logrus.Fatalf("Error connecting to database, %s", err)
	}

	srv := new(server.Server)

	mainRouter := router.InitRoutes()

	navi.NewNavi(db, mainRouter)

	if err := srv.Run(viper.GetString("port"), mainRouter); err != nil {
		logrus.Fatalf("Failed to start server: %v", err)
	}
}

func initConfig() error {
	viper.SetConfigType("yml")
	viper.AddConfigPath("config")
	viper.SetConfigName("config")
	return viper.ReadInConfig()
}
