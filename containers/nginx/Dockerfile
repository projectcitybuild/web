FROM nginx:1.13.6-alpine

WORKDIR /etc/nginx

# overwrite nginx configurations with our own
COPY containers/nginx/conf/nginx.conf .

# set up sites-available and sites-enabled directory structure
RUN mkdir /etc/nginx/sites-available /etc/nginx/sites-enabled
COPY containers/nginx/conf/sites-available /etc/nginx/sites-available
RUN ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf

EXPOSE 80