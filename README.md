# Desk Macros to Zendesk

## Usage
1. Create a `.env` file in the project root with your Desk credentials:

```
DESK_USERNAME="you@example.com"
DESK_PASSWORD="yourdeskpassword"
DESK_URL="https://your-url.desk.com"
```

Note that your Desk user account must have the "API User" role.

2. Run the application over the CLI: `php app.php <command>`.

## Commands
1. `php app.php desk-export` will pull all Macros from Desk and save Quick Reply and Note content in a CSV file. We'll import this CSV into Google Sheets for team editing before importing these to Zendesk.

2. `php app.php custom-actions` returns a report of actions which are *not* Quick Replies or Case Notes. These will need to be replicated in Zendesk manually (there just aren't enough to merit scripting this part). (@TODO)

3. `php app.php zendesk-import` import a given CSV file as Zendesk macros. (@TODO)