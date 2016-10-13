## Network For Good API (Unofficial)

### Environments

1. Testing environment variables

Environment  | Variable
------------ | -------------
NFG_PARTNER_ID  | NFGAPIARYTESTUSER
NFG_PARTNER_PASSWORD  | F5t2GYu#
NFG_PARTNER_SOURCE | NFGAPIARYTEST
NFG_PARTNER_CAMPAIGN | donation donation-reporting

### Tests

1. cd to root app directory

        cd /path/to/network-for-good

2. copy sample database config

        cp .env.example .env

3. to execute tests

        composer test
