#!/bin/bash

# Define the directory path
dir_path="./uploads"

# Define the file to exclude
exclude_file="IAmExample"

# Change directory to the target directory
cd "$dir_path" || exit

# Find and delete everything except the exclude_file
find . -mindepth 1 -maxdepth 1 ! -name "$exclude_file" -exec rm -rf {} +

echo "Cleanup completed."
