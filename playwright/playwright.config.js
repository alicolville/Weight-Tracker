import { defineConfig, devices } from "@playwright/test";

export default defineConfig({
  projects: [
    {
      name: "auth",
      testMatch: /.*\.auth\.js/
    },
    {
      name: "chromium",
      use: {
        ...devices["Desktop Chrome"],
        storageState: ".auth/user.json"
      },
      testMatch: /.*\.test*\.js/,
      dependencies: ["auth"],
    },
  ],
  reporter: "html",
  testDir: "tests",
  timeout: 5000,
});
