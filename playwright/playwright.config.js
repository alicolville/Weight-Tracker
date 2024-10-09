import { defineConfig, devices } from "@playwright/test";

export default defineConfig({
  projects: [
    {
      name: "auth",
      testMatch: /.*\.auth\.ts/
    },
    {
      name: "chromium",
      use: {
        ...devices["Desktop Chrome"],
        storageState: ".auth/user.json"
      },
      testMatch: /.*\.test*\.ts/,
      dependencies: ["auth"],
    },
  ],
  reporter: "html",
  testDir: "tests",
  timeout: 5000,
  workers: 1  // Not ideal, but the data being manipulated by several tests at once was causing sporadic issues.
});
