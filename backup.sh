#!/bin/bash

# ==============================================
# УНИВЕРСАЛЬНЫЙ СКРИПТ БЭКАПА LARAVEL ПРОЕКТА
# Версия: 2.0
# Автор: CRM System
# ==============================================

set -e  # Прерывать выполнение при ошибках

# ==================== НАСТРОЙКИ ====================
CONFIG_FILE="/var/www/crm/backup_config.conf"

# Если конфиг не существует, создаем с настройками по умолчанию
if [ ! -f "$CONFIG_FILE" ]; then
    cat > "$CONFIG_FILE" << 'EOF'
# Конфигурация бэкапа проекта

# Основные пути
PROJECT_PATH="/var/www/crm"
BACKUP_BASE_DIR="/var/www/backups"
LOG_FILE="/var/log/laravel_backup.log"

# Настройки базы данных
DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="crm_system"
DB_USER="root"

# Исключаемые директории (через запятую)
EXCLUDE_DIRS="vendor,node_modules,backups,storage/framework/cache,storage/framework/sessions,storage/framework/views,storage/logs,.git"

# Исключаемые файлы (через запятую)
EXCLUDE_FILES=".env.backup,.env.example,.gitignore,*.log"

# Хранить бэкапов (дней)
KEEP_DAYS=7

# Уведомления (email для отправки отчетов)
NOTIFY_EMAIL="admin@example.com"

# Типы бэкапа (1 - включить, 0 - выключить)
BACKUP_DATABASE=1
BACKUP_CODE=1
BACKUP_STORAGE=1
BACKUP_ENV=1

# Сжатие (tar.gz, zip, или none)
COMPRESSION_TYPE="tar.gz"
EOF
    echo "⚙️  Создан файл конфигурации: $CONFIG_FILE"
    echo "📝 Отредактируйте настройки перед использованием!"
    exit 0
fi

# Загружаем конфигурацию
source "$CONFIG_FILE"

# ==================== ПЕРЕМЕННЫЕ ====================
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_DIR="$BACKUP_BASE_DIR/$TIMESTAMP"
TEMP_DIR="/tmp/backup_${TIMESTAMP}"
LOCK_FILE="/tmp/laravel_backup.lock"

# ==================== ФУНКЦИИ ====================

# Логирование
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Проверка зависимостей
check_dependencies() {
    local deps=("mysqldump" "tar" "gzip")
    for dep in "${deps[@]}"; do
        if ! command -v "$dep" &> /dev/null; then
            log "❌ Ошибка: Не установлена утилита: $dep"
            exit 1
        fi
    done
    log "✅ Все зависимости проверены"
}

# Проверка блокировки
check_lock() {
    if [ -f "$LOCK_FILE" ]; then
        log "⚠️  Бэкап уже выполняется. Блокировка: $LOCK_FILE"
        exit 1
    fi
    echo $$ > "$LOCK_FILE"
    trap 'rm -f "$LOCK_FILE"; exit' INT TERM EXIT
}

# Проверка директорий
check_directories() {
    if [ ! -d "$PROJECT_PATH" ]; then
        log "❌ Ошибка: Директория проекта не найдена: $PROJECT_PATH"
        exit 1
    fi
    
    mkdir -p "$BACKUP_DIR" "$TEMP_DIR"
    log "✅ Директории проверены и созданы"
}

# Бэкап базы данных
backup_database() {
    if [ "$BACKUP_DATABASE" -eq 0 ]; then
        log "⏭️  Бэкап базы данных пропущен"
        return 0
    fi
    
    log "💾 Начинаю бэкап базы данных: $DB_NAME"
    
    local db_file="$TEMP_DIR/database.sql"
    
    # Пытаемся получить пароль из .env
    local db_password=""
    if [ -f "$PROJECT_PATH/.env" ]; then
        db_password=$(grep -oP 'DB_PASSWORD=\K[^$]+' "$PROJECT_PATH/.env" | head -1)
    fi
    
    # Варианты подключения к БД
    if [ -n "$db_password" ]; then
        if mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$db_password" "$DB_NAME" > "$db_file" 2>/dev/null; then
            log "✅ База данных успешно экспортирована"
        else
            log "⚠️  Не удалось экспортировать БД с паролем из .env, пробую без пароля"
            mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" > "$db_file" 2>&1 || {
                log "❌ Ошибка экспорта базы данных"
                return 1
            }
        fi
    else
        mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" > "$db_file" 2>&1 || {
            log "❌ Ошибка экспорта базы данных"
            return 1
        }
    fi
    
    # Проверяем размер файла
    local file_size=$(stat -c%s "$db_file" 2>/dev/null || stat -f%z "$db_file")
    if [ "$file_size" -lt 100 ]; then
        log "⚠️  Файл БД слишком мал, возможно ошибка экспорта"
    fi
    
    log "✅ Бэкап базы данных завершен ($(numfmt --to=iec "$file_size"))"
}

# Бэкап кода проекта
backup_code() {
    if [ "$BACKUP_CODE" -eq 0 ]; then
        log "⏭️  Бэкап кода пропущен"
        return 0
    fi
    
    log "📦 Начинаю бэкап кода проекта"
    
    cd "$PROJECT_PATH" || exit 1
    
    # Создаем файл исключений
    local exclude_file="$TEMP_DIR/exclude.list"
    echo "$EXCLUDE_DIRS" | tr ',' '\n' > "$exclude_file"
    echo "$EXCLUDE_FILES" | tr ',' '\n' >> "$exclude_file"
    
    # Бэкап с исключениями
    if tar --exclude-from="$exclude_file" -czf "$TEMP_DIR/code.tar.gz" . 2>/dev/null; then
        log "✅ Код проекта успешно заархивирован"
    else
        log "❌ Ошибка архивации кода проекта"
        return 1
    fi
}

# Бэкап storage
backup_storage() {
    if [ "$BACKUP_STORAGE" -eq 0 ]; then
        log "⏭️  Бэкап storage пропущен"
        return 0
    fi
    
    log "📁 Начинаю бэкап storage"
    
    local storage_path="$PROJECT_PATH/storage/app"
    if [ -d "$storage_path" ]; then
        # Исключаем cache, sessions, views
        if tar --exclude="cache/*" --exclude="sessions/*" --exclude="views/*" --exclude="logs/*" \
             -czf "$TEMP_DIR/storage.tar.gz" -C "$storage_path" . 2>/dev/null; then
            log "✅ Storage успешно заархивирован"
        else
            log "⚠️  Ошибка архивации storage"
        fi
    else
        log "⚠️  Директория storage не найдена"
    fi
}

# Бэкап .env файла
backup_env() {
    if [ "$BACKUP_ENV" -eq 0 ]; then
        log "⏭️  Бэкап .env пропущен"
        return 0
    fi
    
    log "🔐 Начинаю бэкап .env файла"
    
    local env_file="$PROJECT_PATH/.env"
    if [ -f "$env_file" ]; then
        cp "$env_file" "$TEMP_DIR/.env"
        # Создаем безопасную версию без паролей для отладки
        grep -v -E '(PASSWORD|SECRET|KEY|TOKEN)' "$env_file" > "$TEMP_DIR/.env.sample" 2>/dev/null || true
        log "✅ .env файл скопирован"
    else
        log "⚠️  .env файл не найден"
    fi
}

# Создание информации о бэкапе
create_backup_info() {
    log "📝 Создаю информацию о бэкапе"
    
    cat > "$TEMP_DIR/backup_info.txt" << EOF
==========================================
БЭКАП LARAVEL ПРОЕКТА
==========================================
Дата создания: $(date)
Проект: $(basename "$PROJECT_PATH")
Директория: $PROJECT_PATH
Тип бэкапа: Полный
Таймстамп: $TIMESTAMP

КОМПОНЕНТЫ БЭКАПА:
✅ База данных: $([ "$BACKUP_DATABASE" -eq 1 ] && echo "Да" || echo "Нет")
✅ Код проекта: $([ "$BACKUP_CODE" -eq 1 ] && echo "Да" || echo "Нет") 
✅ Storage: $([ "$BACKUP_STORAGE" -eq 1 ] && echo "Да" || echo "Нет")
✅ .env файл: $([ "$BACKUP_ENV" -eq 1 ] && echo "Да" || echo "Нет")

ИНФОРМАЦИЯ О ПРОЕКТЕ:
Версия PHP: $(php -v 2>/dev/null | head -1 || echo "Не доступно")
Свободное место: $(df -h "$BACKUP_BASE_DIR" | tail -1 | awk '{print $4}')
Размер проекта: $(du -sh "$PROJECT_PATH" 2>/dev/null | cut -f1 || echo "Не доступно")

БАЗА ДАННЫХ:
Имя: $DB_NAME
Хост: $DB_HOST
Порт: $DB_PORT
Пользователь: $DB_USER

==========================================
EOF
}

# Создание финального архива
create_final_archive() {
    log "🎯 Создаю финальный архив"
    
    cd "$TEMP_DIR" || exit 1
    
    case "$COMPRESSION_TYPE" in
        "tar.gz")
            local final_file="$BACKUP_DIR/${TIMESTAMP}.tar.gz"
            tar -czf "$final_file" .
            ;;
        "zip")
            local final_file="$BACKUP_DIR/${TIMESTAMP}.zip"
            zip -rq "$final_file" .
            ;;
        "none")
            local final_file="$BACKUP_DIR/${TIMESTAMP}"
            cp -r . "$final_file"
            ;;
        *)
            log "⚠️  Неизвестный тип сжатия, использую tar.gz"
            local final_file="$BACKUP_DIR/${TIMESTAMP}.tar.gz"
            tar -czf "$final_file" .
            ;;
    esac
    
    local final_size=$(du -h "$final_file" | cut -f1)
    log "✅ Финальный архив создан: $final_file ($final_size)"
    
    echo "$final_file" > "$BACKUP_DIR/latest_backup.txt"
}

# Очистка старых бэкапов
cleanup_old_backups() {
    if [ "$KEEP_DAYS" -gt 0 ]; then
        log "🧹 Очищаю старые бэкапы (старше $KEEP_DAYS дней)"
        find "$BACKUP_BASE_DIR" -maxdepth 1 -name "*.tar.gz" -type f -mtime "+$KEEP_DAYS" -delete
        find "$BACKUP_BASE_DIR" -maxdepth 1 -name "*.zip" -type f -mtime "+$KEEP_DAYS" -delete
        find "$BACKUP_BASE_DIR" -maxdepth 1 -name "20*" -type d -mtime "+$KEEP_DAYS" -exec rm -rf {} + 2>/dev/null || true
        log "✅ Очистка завершена"
    fi
}

# Отправка уведомления (заглушка)
send_notification() {
    if [ -n "$NOTIFY_EMAIL" ] && [ "$NOTIFY_EMAIL" != "admin@example.com" ]; then
        log "📧 Отправляю уведомление на $NOTIFY_EMAIL"
        # Здесь можно добавить отправку email
        # mail -s "Бэкап проекта завершен" "$NOTIFY_EMAIL" < "$TEMP_DIR/backup_info.txt"
    fi
}

# Основная функция
main() {
    log "🚀 ЗАПУСК УНИВЕРСАЛЬНОГО БЭКАПА"
    
    check_dependencies
    check_lock
    check_directories
    
    # Выполняем бэкапы
    backup_database
    backup_code
    backup_storage
    backup_env
    
    create_backup_info
    create_final_archive
    cleanup_old_backups
    send_notification
    
    # Очистка временных файлов
    rm -rf "$TEMP_DIR"
    rm -f "$LOCK_FILE"
    
    log "✅ БЭКАП УСПЕШНО ЗАВЕРШЕН"
    log "📁 Результат: $BACKUP_DIR"
    
    # Статистика
    local total_size=$(du -sh "$BACKUP_BASE_DIR" | cut -f1)
    local backup_count=$(find "$BACKUP_BASE_DIR" -name "*.tar.gz" -o -name "*.zip" | wc -l)
    log "📊 Статистика: $backup_count бэкапов, общий размер: $total_size"
}

# ==================== ЗАПУСК ====================
main "$@"
