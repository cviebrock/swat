pipeline {
    agent any
    stages {
        stage('Install Composer Dependencies') {
            steps {
                sh 'rm -rf composer.lock vendor/'
                sh 'composer install'
            }
        }

        stage('Install Yarn Dependencies') {
            environment {
                YARN_CACHE_FOLDER = "${env.WORKSPACE}/yarn-cache/${env.BUILD_NUMBER}"
            }
            steps {
                sh 'yarn install'
            }
        }

        stage('Lint Modified Files') {
            when {
                not {
                    branch 'master'
                }
            }
            steps {
                sh '''
                    master_sha=$(git rev-parse origin/master)
                    newest_sha=$(git rev-parse HEAD)
                    ./vendor/bin/phpcs \
                    --standard=SilverorangeTransitionalPrettier \
                    --tab-width=4 \
                    --encoding=utf-8 \
                    --warning-severity=0 \
                    --extensions=php \
                    $(git diff --diff-filter=ACRM --name-only $master_sha...$newest_sha)
                '''
            }
        }

        stage('Check PHP code style') {
            when {
                branch 'master'
            }
            steps {
                sh 'composer run phpcs'
            }
        }

        stage('Check if pretty') {
            steps {
                sh 'yarn run prettier'
            }
        }
    }
}
