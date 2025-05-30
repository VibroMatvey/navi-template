FROM postgis/postgis:latest

RUN apt-get update && \
    apt-get install -y postgresql-17-pgrouting && \
    apt-get clean

COPY init-pgrouting.sh /docker-entrypoint-initdb.d/10_init-pgrouting.sh