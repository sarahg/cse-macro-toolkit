# Migrate Desk Macros

## Usage
1. Clone this repo and run `composer install` to pull down dependencies.
2. Create a `.env` file in the project root with your Desk and Zendesk credentials:

```
DESK_USERNAME="you@example.com"
DESK_PASSWORD="yourdeskpassword"
DESK_URL="https://your-url.desk.com"

ZENDESK_EMAIL="you@example.com"
ZENDESK_PASSWORD="yourzendeskpassword"
ZENDESK_SUBDOMAIN="your-url"
ZENDESK_IMPORT_FILE="exports/somefile.json"
```

Note that your Desk user account must have the "API User" role.

3. Run the application over the CLI: `php app.php <command>`.

## Commands
1. `php app.php export-replies` retrieves all Macros from Desk and exports Quick Reply/Note content to a CSV file. We'll import this CSV into Google Sheets for team editing before importing these to Zendesk.

2. `php app.php export-full` retrieves all Macros and their associated Actions from Desk and exports these to a JSON file.

3. `php app.php zendesk-import` imports Macros in a given JSON file to Zendesk. Specify your JSON file with the `ZENDESK_IMPORT_FILE` environment variable.