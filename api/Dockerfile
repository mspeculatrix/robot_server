# Dockerfile for sheldon. This is used when building a new image.

FROM node:alpine

# Copy files to container
COPY app_files/ .
RUN npm install

CMD npm run start
