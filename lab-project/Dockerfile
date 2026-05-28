FROM node:18-slim

RUN apt-get update \
 && apt-get install -y --no-install-recommends ca-certificates unzip wget \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /home/ubuntu

RUN wget -q https://aws-tc-largeobjects.s3.us-west-2.amazonaws.com/CUR-TF-200-ACCAP1-1-91571/1-lab-capstone-project-1/code.zip \
 && unzip -q code.zip -x "resources/codebase_partner/node_modules/*" \
 && rm code.zip

WORKDIR /home/ubuntu/resources/codebase_partner

RUN npm install --omit=dev \
 && npm install aws aws-sdk

ENV APP_PORT=3000
EXPOSE 3000

CMD ["npm", "start"]
