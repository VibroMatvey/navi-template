FROM golang:1.22.5-alpine

WORKDIR /app

COPY go.mod go.sum ./
RUN go mod download

COPY . .

EXPOSE 8008
CMD ["sh", "-c", "go build -o ./bin/app ./cmd/main.go && ./bin/app"]