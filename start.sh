#!/bin/bash
echo "start MySql and LocalStack"

docker-compose up -d

sleep 10

aws --endpoint-url=http://localhost:4566 s3 mb s3://imagens
