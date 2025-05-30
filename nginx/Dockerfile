FROM nginx:alpine AS runtime

RUN apk add --no-cache curl

COPY ./nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./nginx/conf.d/* /etc/nginx/conf.d/

RUN mkdir -p /var/www/app/public && \
    mkdir -p /var/run/nginx && \
    chown -R nginx:nginx /var/www/app && \
    chown -R nginx:nginx /var/run/nginx && \
    chown -R nginx:nginx /var/log/nginx

USER nginx

WORKDIR /var/www/app
COPY --chown=nginx ./php/public/ /var/www/app/public/

EXPOSE 8080

CMD ["nginx", "-g", "daemon off;"]