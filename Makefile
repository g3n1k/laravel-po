# ==========================
# CONFIG
# ==========================
DC=docker compose
PREFIX=po_
APP=$(PREFIX)app
QUEUE=queue
SCHEDULER=scheduler

ENV?=local

d?=

.DEFAULT_GOAL := help
.SILENT:

ifeq ($(strip $(d)),)
prefix :=
else
prefix := po_
endif


# ==========================
# HELP
# ==========================
help:
	echo ""
	echo "Usage:"
	echo "  make up | down | restart | build | log | ps"
	echo ""
	echo "Laravel:"
	echo "  make artisan <cmd>"
	echo "  make composer <cmd>"
	echo ""
	echo "Services:"
	echo "  make bash"
	echo "  make queue-bash"
	echo ""
	echo "Queue / Scheduler:"
	echo "  make queue-restart"
	echo "  make scheduler-run"
	echo ""
	echo "Deploy:"
	echo "  make deploy"
	echo ""

# ==========================
# DOCKER
# ==========================
up:
	$(DC) up -d $(prefix)$(d)

down:
	$(DC) down $(prefix)$(d)

restart: down up log
# 	$(DC) down && $(DC) up -d

build:
	$(DC) build --no-cache $(prefix)$(d)

docker-build:
	docker build -t g3n1k/3ddm-po -f Dockerfile .

log:
	$(DC) logs -f $(prefix)$(d)

ps: 
	$(DC) ps
	
in: 	
	$(DC) exec $(prefix)$(d) sh


# ==========================
# CONTAINER ACCESS
# ==========================
bash:
	$(DC) exec $(APP) sh

queue-bash:
	$(DC) exec $(QUEUE) sh

# ==========================
# LARAVEL (ARGS DIRECT)
# ==========================
artisan:
	$(DC) exec $(APP) php artisan $(filter-out $@,$(MAKECMDGOALS))

composer:
	$(DC) exec $(APP) composer $(filter-out $@,$(MAKECMDGOALS))

# Build application dependencies and assets
build-app:
	$(DC) exec $(APP) composer install
	$(DC) exec $(APP) php artisan key:generate
	$(DC) exec $(APP) php artisan config:cache
	$(DC) exec $(APP) php artisan route:cache
	$(DC) exec $(APP) php artisan view:cache

# ==========================
# QUEUE & SCHEDULER
# ==========================
queue-restart:
	$(DC) exec $(APP) php artisan queue:restart

scheduler-run:
	$(DC) exec $(APP) php artisan schedule:run

# ==========================
# PRODUCTION DEPLOY
# ==========================
deploy:
	echo "🚀 Deploying ($(ENV))..."
	$(DC) git pull
# 	$(DC) up -d --build
	$(DC) exec $(APP) php artisan migrate --force
	$(DC) exec $(APP) php artisan optimize
# 	$(DC) exec $(APP) php artisan queue:restart
	echo "✅ Deploy finished"


# =========================
# REMOTE UPDATE PRODUCTION
# =========================
remote-update:
	ansible-playbook -i ansible/inventory.ini ansible/deploy.yml


# ==========================
# CATCH-ALL (IMPORTANT)
# ==========================
%:
	@:
