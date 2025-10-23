pipeline {
    agent any

    tools {
        nodejs 'Node20'
    }

    environment {
        LARAVEL_DIR = 'smarthealth-tracker-laravel'
        DOCKER_IMAGE = "siwarkrizii/smarthealth-tracker:${BUILD_NUMBER}"
    }

    stages {
        stage('Checkout') {
            steps {
                echo '🚀 Cloning repository...'
                git branch: 'siwar_new_integration',
                    url: 'https://github.com/KriziSiwar/smarthealth-tracker-laravel.git',
                    credentialsId: 'GithubToken-laravel'
            }
        }

        stage('Setup Laravel') {
            steps {
                echo '🔧 Setting up Laravel...'
                dir(env.LARAVEL_DIR) {
                    sh 'cp .env.example .env'
                    sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                    sh 'php artisan key:generate'
                    sh 'php artisan breeze:install blade --pest --ssr --dark'
                }
            }
        }

        stage('Setup Frontend') {
            steps {
                echo '🔄 Setting up frontend...'
                dir(env.LARAVEL_DIR) {
                    sh 'npm install --no-audit --prefer-offline'
                }
            }
        }

        stage('Build Frontend') {
            steps {
                echo '🛠️ Building frontend assets...'
                dir(env.LARAVEL_DIR) {
                    sh 'export VITE_DISABLE_OPEN_BROWSER=true'
                    sh 'npm run build'
                }
            }
        }

        stage('Run Tests') {
            steps {
                echo '🧪 Running tests...'
                dir(env.LARAVEL_DIR) {
                    sh 'touch database/database.sqlite'
                    sh 'chmod 666 database/database.sqlite'
                    sh 'php artisan migrate:fresh --force'
                    sh 'php artisan test'
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo '🐳 Building Docker image...'
                    sh "docker build -t ${env.DOCKER_IMAGE} ."
                }
            }
        }

        stage('Push to Docker Hub') {
            when {
                branch 'siwar_new_integration'
            }
            steps {
                script {
                    withCredentials([usernamePassword(
                        credentialsId: 'docker-hub-credentials',
                        usernameVariable: 'DOCKER_USERNAME',
                        passwordVariable: 'DOCKER_PASSWORD'
                    )]) {
                        sh """
                            echo ${DOCKER_PASSWORD} | docker login -u ${DOCKER_USERNAME} --password-stdin
                            docker push ${env.DOCKER_IMAGE}
                        """
                    }
                }
            }
        }

        stage('Deploy') {
            when {
                branch 'siwar_new_integration'
            }
            steps {
                echo '🚀 Deploying with Docker Compose...'
                sh 'docker-compose down || true'
                sh 'docker-compose up -d'
            }
        }
    }

    post {
        always {
            echo '🧹 Cleaning up...'
            sh 'docker container prune -f'
            sh 'docker image prune -f'
        }
    }
}
