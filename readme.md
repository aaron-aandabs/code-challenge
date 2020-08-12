# Elevator Controls

## Prerequisites
- Copy the .env.sample file to .env and configure you elevator/floor/database information
- Run composer update

## Usage
Do a GET call to /call/{start_floor_number}/to/{end_floor_number}. The program will automatically decide
which elevator is closest to you and bring that elevator to you. It will then update the database for
so that we can reference where the elevators are currently.
