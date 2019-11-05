# Desk Macros to Zendesk
This is a work in progress, not ready for production usage yet.

## Usage
1. Copy `secrets.example.json` to `secrets.json`, and fill in your Desk API and Google Sheets credentials.
2. Run the application over the CLI: `php app.php` with the `--action` parameter.

## Commands
1. `php app.php --action=macros` will pull all Macros from Desk and save this data as a JSON file in the
`exports` directory within the app.

Macros include:
- Title
- Description
- Folder(s)
- Action(s)

Actions include:
- Action type
- Title
- Value

2. `php app.php --action=quickreplies` will pull macros as described above, then from those JSON files, export Quick Reply contents to a Google Spreadsheet. We're using this to allow our entire team to contribute content edits before we migrate these macros into Zendesk.

3. `php app.php --action=zendesk` will create a build of exported macros and merge in new content from the Google Sheet created in step 2, then import this data to Zendesk.

## Full migration process
1. `php app.php --action=macros`
2. `php app.php --action=zendesk`