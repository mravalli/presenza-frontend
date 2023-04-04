FROM nginx:alpine as base

###### base stage ######
ENV FASTCGI_TARGET='backend:9000'

RUN apk add --no-cache curl
RUN mkdir /app

###### dev stage ######
FROM base as dev

COPY nginx.dev.conf /etc/nginx/default.tmpl
HEALTHCHECK --interval=30s --timeout=2s CMD curl -f localhost/nginx_status || exit 1

CMD [ "/bin/sh", "-c", "envsubst '${FASTCGI_TARGET} ${DOCS_TARGET}' < /etc/nginx/default.tmpl > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;' || cat /etc/nginx/conf.d/default.conf" ]

###### production stage ######
FROM base

COPY nginx.production.conf /etc/nginx/default.tmpl
COPY dist /app/
HEALTHCHECK --interval=30s --timeout=2s CMD curl -f localhost/nginx_status || exit 1

CMD [ "/bin/sh", "-c", "envsubst '${FASTCGI_TARGET} ${DOCS_TARGET}' < /etc/nginx/default.tmpl > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;' || cat /etc/nginx/conf.d/default.conf" ]