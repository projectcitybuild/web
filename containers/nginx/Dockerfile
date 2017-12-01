FROM nginx:1.13.6-alpine

WORKDIR  /etc/nginx

# overwrite nginx configurations with our own
COPY conf/nginx.conf .

# set up sites-available and sites-enabled directory structure
RUN mkdir ./sites-available ./sites-enabled
COPY conf/sites-available ./sites-available
RUN ln -s ./sites-available/default.conf ./sites-enabled/default.conf

EXPOSE 80