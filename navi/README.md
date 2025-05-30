# Navi api service

### Install dependencies

```shell
go mod tidy
```

### Generate docs

```shell
go install github.com/swaggo/swag/cmd/swag@latest
```

```shell
swag init -g ./cmd/main.go
```

### Run and build service

```shell
cd db && db compose up --build -d
```

```shell
go run ./cmd/main.go
```

windows

```shell
GOOS=windows GOARCH=amd64 go build -o ./bin/api.exe ./cmd/main.go
```

mac os

```shell
GOARCH=arm64 GOOS=darwin go build -o ./bin/api ./cmd/main.go
```