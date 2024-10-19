#!/bin/bash

for i in {1..1000}; do
  echo "hello test me bro" | curl -F 'kmi=<-' http://127.0.0.1:8080/paste
done