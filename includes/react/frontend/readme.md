### Development
To work on the React code, follow these steps:

1. In your terminal, navigate to /react/frontend/ folder
2. Install all Node packages from `package.json` file, using either of the following commands:
    1. Install WP-Scripts npm install @wordpress/scripts --save-dev
    1. Install packages __locally__ (only in this theme folder on your machine), run `npm install`
    2. Install packages __globally__ on your machine, run `npm install -g`
3. Run `npm run watch` to run the development 'watch' script; this will track every change made to the React application and rebuild it in real time

When finished working on the application, simply click `ctrl`+`c` to exit the `npm run watch` process.

### Build
Before deploying any updates to the application, follow this final step:

1. In the __LookUp__ theme path, run `npm run build`

This runs a dependency script, `wp-scripts build` (inside `package.json`), which will minify all application JS before deploying.

(This bypasses the usual React [JS bundling](https://create-react-app.dev/docs/production-build) that this command would normally entail.)