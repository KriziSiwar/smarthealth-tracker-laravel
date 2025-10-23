# Build stage
FROM node:20 AS node_build
WORKDIR /app
COPY smarthealth-tracker-laravel/package*.json ./
RUN npm install
COPY smarthealth-tracker-laravel/ .
RUN npm run build

# Production stage
FROM php:8.2-fpm

# ... rest of your Dockerfile ...
