# Central Hall

Central Hall is a Wordpress plugin and PFSense "captive portal" solution to manage access to a network via Wordpress login.

## Building

Before releasing the plugin, the underlying Angular app must be built.  To do that, first make sure you
have `npm` and `bower` installed.  Then run the following commands:

    npm install
    bower install
    grunt build

This will:

    - Download all the files need to build the app
    - Download dependencies (like jQuery/Bootstrap/Angular)
    - Create an optimized version of the app

Once the `portal/build/` directory has been created, you can upload the plugin.

## TODO

- [x] Implement login actions via an Angular service
- [X] Complete login action in Wordpress plugin
- [X] Complete guest action in Wordpress plugin
- [ ] Build settings page:
  - [ ] Allow for logo upload
  - [ ] Allow for custom terms
  - [ ] Allow for "lost password" instructions
  - [x] Let download the boostrap HTML file
    - [x] Needs to replace references to CSS/JS with script-loader.js