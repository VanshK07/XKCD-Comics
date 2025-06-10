#!/bin/bash

# setup_cron.sh - Automatically configure CRON job for GitHub timeline updates

# Get the absolute path of the current directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_PHP_PATH="$SCRIPT_DIR/cron.php"

# Check if cron.php exists
if [ ! -f "$CRON_PHP_PATH" ]; then
    echo "Error: cron.php not found at $CRON_PHP_PATH"
    exit 1
fi

# Define the cron job (runs every 5 minutes)
CRON_JOB="*/5 * * * * /usr/bin/php $CRON_PHP_PATH >> $SCRIPT_DIR/cron_output.log 2>&1"

# Check if the cron job already exists
if crontab -l 2>/dev/null | grep -q "$CRON_PHP_PATH"; then
    echo "CRON job for GitHub timeline updates already exists."
    echo "Current crontab entries related to this script:"
    crontab -l | grep "$CRON_PHP_PATH"
else
    # Add the cron job
    echo "Adding CRON job to run every 5 minutes..."
    
    # Get current crontab content (if any)
    CURRENT_CRONTAB=$(crontab -l 2>/dev/null)
    
    # Create new crontab with the additional job
    {
        echo "$CURRENT_CRONTAB"
        echo "$CRON_JOB"
    } | crontab -
    
    if [ $? -eq 0 ]; then
        echo "✅ CRON job successfully added!"
        echo "Job details: $CRON_JOB"
        echo ""
        echo "The script will now run every 5 minutes and:"
        echo "- Fetch GitHub timeline data"
        echo "- Send HTML formatted emails to all registered users"
        echo "- Log execution details to cron_output.log"
        echo ""
        echo "To verify the cron job is active, run: crontab -l"
        echo "To remove the cron job later, run: crontab -e and delete the line"
    else
        echo "❌ Failed to add CRON job. Please check your system permissions."
        exit 1
    fi
fi

# Create initial log files with proper permissions
touch "$SCRIPT_DIR/cron_output.log"
touch "$SCRIPT_DIR/cron.log"
chmod 644 "$SCRIPT_DIR/cron_output.log"
chmod 644 "$SCRIPT_DIR/cron.log"

# Create registered_emails.txt if it doesn't exist
if [ ! -f "$SCRIPT_DIR/registered_emails.txt" ]; then
    touch "$SCRIPT_DIR/registered_emails.txt"
    chmod 644 "$SCRIPT_DIR/registered_emails.txt"
    echo "Created registered_emails.txt file for storing email addresses."
fi

echo ""
echo "Setup complete! The CRON job is now configured and will start running automatically."
echo "Check the log files for execution details:"
echo "- cron_output.log: General CRON output"
echo "- cron.log: Application-specific logs"