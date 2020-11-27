FROM nginx:alpine

ENV FASTCGI_TARGET='app:9000'

COPY nginx.conf /etc/nginx/default.tmpl
COPY dist /app/

RUN apk add --no-cache curl

HEALTHCHECK --interval=30s --timeout=2s CMD curl -f localhost/nginx_status || exit 1

CMD [ "/bin/sh", "-c", "envsubst '${FASTCGI_TARGET} ${DOCS_TARGET}' < /etc/nginx/default.tmpl > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;' || cat /etc/nginx/conf.d/default.conf" ]

