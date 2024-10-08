# URL crawler

## Description
an application to store a list of URLs and retrieve information from
those URLs using a backend process.

## Installation

### Execute in folder
1. Clone the repository:
   ```bash
   git clone https://github.com/rucaua/simple-crawler.git
   ```
   
2. Navigate into the project directory:
   ```bash
   cd simple-crawler
   ```
   
3. run Docker image 
   ```bash
   docker compose up
   ```

4. Run inside Yii container to initialize application
   ```bash
   php init
   ```
   
### Execute inside Docker "yii" container
1. Install dependencies (Run inside "yii" container)
   ```bash
   composer install
   ```
2. Run migrations (Run inside "yii" container)
   ```bash
   php yii migrate
   ```

## Usage

Access UI by opening:  http://localhost:31080/

Submit URL.

To start crawling run CLI command (Run inside "yii" container):
   ```bash
   php yii crawler/run
   ```
The command above crawls one URL at a time, searching for internal URLs and adds them to the queue, but don't execute until next run.


*for production, this process should be automated using CRON or other scheduler.*



