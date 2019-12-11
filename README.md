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
```

Note that your Desk user account must have the "API User" role.

3. Run the application over the CLI: `php app.php <command>`.

## Commands
1. `php app.php desk-export` will pull all Macros from Desk and save Quick Reply and Note content in a CSV file. We'll import this CSV into Google Sheets for team editing before importing these to Zendesk.

2. `php app.php custom-actions` returns a report of actions which are *not* Quick Replies or Case Notes. These will need to be replicated in Zendesk manually (there just aren't enough to merit scripting this part). (@TODO)

3. `php app.php zendesk-import imports/filename.csv` imports Macros in a given CSV file to Zendesk. (in-progress)

## Workflow

0. Set up the app on your local machine (steps 1-2 under Usage above).
1. Run the export command.
2. Import the CSV generated in step 1 to Google Sheets for the team to review Macro content. We added columns on the spreadsheet for Status (Needs Review / Done / Deprecate) and Assignee.
3. Once reviews are done, export the Google Sheet to CSV.
4. Run the CSV through the import script.
