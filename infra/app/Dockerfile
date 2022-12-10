FROM golang:latest

WORKDIR /app
COPY ./app /app/

RUN go mod init main \
  && go mod tidy \
  && go build

ENV CGO_ENABLED=0 \
  GOOS=linux \
  GOARCH=amd64
EXPOSE 8080

CMD ["go", "run", "main.go"]
