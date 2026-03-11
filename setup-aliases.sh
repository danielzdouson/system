#!/bin/bash

# SACCO System Aliases Setup
# Run this once to add aliases to your shell

echo "Setting up SACCO System aliases..."

# Add to .bashrc
cat >> ~/.bashrc << 'EOF'

# SACCO System Aliases
alias sacco-start='cd /home/danielz/projects/system && docker-compose down && docker-compose up -d --force-recreate && sleep 30 && docker exec saco_system_php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && docker exec saco_system_php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && echo "✅ SACCO System started successfully!"'
alias sacco-stop='cd /home/danielz/projects/system && docker-compose down && echo "🛑 SACCO System stopped"'
alias sacco-restart='sacco-stop && sacco-start'
alias sacco-status='cd /home/danielz/projects/system && docker-compose ps && echo "📊 SACCO System status above"'
alias sacco-logs='cd /home/danielz/projects/system && docker logs saco_system_php --tail 20'
alias sacco-test='curl -f http://localhost:8080 > /dev/null 2>&1 && echo "✅ SACCO System is working!" || echo "❌ SACCO System has issues"'
alias sacco='cd /home/danielz/projects/system'

# Quick navigation
alias sacco-dir='cd /home/danielz/projects/system'

EOF

echo "✅ Aliases added to ~/.bashrc"
echo "🔄 Reload your shell with: source ~/.bashrc"
echo ""
echo "📋 Available commands:"
echo "  sacco-start    - Start the system with permission fixes"
echo "  sacco-stop     - Stop all containers"
echo "  sacco-restart  - Stop and start the system"
echo "  sacco-status   - Check container status"
echo "  sacco-logs     - View recent logs"
echo "  sacco-test     - Test if system is working"
echo "  sacco          - Go to project directory"
echo "  sacco-dir      - Go to project directory"
