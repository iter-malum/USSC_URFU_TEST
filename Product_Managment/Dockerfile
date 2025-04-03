FROM python:3.7-alpine

ENV PYTHONPATH "${PYTHONPATH}:/casp-pm"
ENV FLASK_APP=app.py
ENV FLASK_RUN_HOST=0.0.0.0
ENV PG_DB_SERVER=0.0.0.0
ENV PG_DB_DB=casp-pm
ENV PG_DB_USERNAME=postgres
ENV PG_DB_PASSWORD=root
ENV MONGO_SERVER=0.0.0.0
ENV MONGO_DB=casp-pm

ADD . /casp-pm
WORKDIR /casp-pm

COPY requirements.txt requirements.txt
RUN apk update \
    && apk add gcc musl-dev libffi-dev openssl-dev libpq-dev python3-dev bash \
    && pip install -r requirements.txt

EXPOSE 8000
CMD [ "flask","--debug", "run","-p","8000" ]
