# CSE Macro Toolkit 

CLI tools to manage macros in bulk.

## Usage
1. Clone this repo and run `composer install` to pull down dependencies.
2. Create a `.env` file in the project root with your Zendesk credentials:

```
ZENDESK_EMAIL="you@example.com"
ZENDESK_PASSWORD="yourzendeskpassword"
ZENDESK_SUBDOMAIN="your-url"
ZENDESK_IMPORT_FILE="exports/somefile.json"
```

3. Run the application over the CLI: `php app.php <command>`.

## Roadmap
- Pull out migration-related code and references to Desk.
- Add commands to work with Intercom.
