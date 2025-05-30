package database

import (
	"github.com/VibroMatvey/postgis-go-navi/internal/navi"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
	"log"
	"os"
)

type Config struct {
	Dsn string
}

func NewMysqlDB(cfg Config) (*gorm.DB, error) {
	db, err := gorm.Open(postgres.New(postgres.Config{
		DSN: cfg.Dsn,
	}), &gorm.Config{})

	if err != nil {
		log.Printf(os.Getenv("DATABASE_URL"))
		return nil, err
	}

	err = db.AutoMigrate(&navi.Road{}, &navi.Area{}, &navi.MapPoint{}, &navi.Terminal{})

	if err != nil {
		return nil, err
	}

	return db, nil
}
