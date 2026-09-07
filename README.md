<div align="center">

# 🐦‍⬛ RavenWarp

### *Fly the Realms. Share the Signal.*

A secure, next-generation social network fusing Norse mythology with bold sci-fi futurism — built in the open as part of a public 5-way AI development experiment.

[![License: MIT](https://img.shields.io/badge/License-MIT-blueviolet.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%205-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Font Awesome](https://img.shields.io/badge/Icons-Font%20Awesome-528DD7?logo=fontawesome&logoColor=white)](https://fontawesome.com/)
[![Status](https://img.shields.io/badge/Status-In%20Development-orange.svg)]()
[![Security VDP](https://img.shields.io/badge/Security-HackerOne%20%2F%20BugCrowd-red?logo=hackerone&logoColor=white)]()
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)]()
[![Made with](https://img.shields.io/badge/Made%20with-Vikings%20%26%20Warp%20Drives-2b2b2b.svg)]()

</div>

---

## 📜 Table of Contents

- About RavenWarp
- The AI Build-Off Experiment
- Core Features
- Free vs Premium Tiers
- Tech Stack
- Security & Encryption Philosophy
- System Architecture
- Admin Command Deck
- Reputation & Badge System
- Roadmap
- Hosting Environment
- Contributing
- Responsible Disclosure
- License
- Acknowledgments
- Connect With Us

---

## 🛰️ About RavenWarp

RavenWarp takes its name from Huginn and Muninn, the twin ravens of Norse mythology who fly across the Nine Realms each day and return to whisper everything they've seen and heard. That is, at its core, what a social network is — a flock of signals crossing the void and landing back home. We paired that ancient idea with a modern warp-speed sensibility, drawing visual and tonal inspiration from Star Wars, Star Trek, Stargate, Rick and Morty, Back to the Future, and classic Viking longship culture.

RavenWarp is being designed from the ground up to be fast, elegant, heavily secured, and genuinely fun to use — a place for posts, conversations, communities, and reputation, wrapped in a Sci-Fi Viking aesthetic unlike anything else out there.

---

## 🧪 The AI Build-Off Experiment

RavenWarp is more than a product — it is the subject of a public case study. The same brief has been handed to five separate AI systems to see how each approaches architecture, security, design, and documentation for an identical set of requirements. The competing systems are:

- GitHub Copilot
- Google Gemini
- OpenAI ChatGPT
- Anthropic Claude
- DeepSeek

Progress, decisions, and lessons learned are being documented in real time in a companion article series published on the Bearded Viking website and on Medium, with build updates shared across social media as development unfolds. The best-performing build, judged by real user activity after launch, wins the right to go live on a paid public domain, receive a fully public GitHub history of commits and pull requests, and move forward into professional security testing.

---

## ⭐ Core Features

**Social Graph & Interaction**
Friend requests, follower relationships, posts, threaded comments, likes and reactions, and shareable profiles form the social backbone of the platform.

**Messaging**
Real-time private one-to-one messaging alongside group conversations, built for instant back-and-forth without page reloads.

**Reputation & Recognition**
A points-based reputation system rewards good-standing community members, paired with a badge system that visually showcases achievements, milestones, and standing on a user's profile.

**Activity Transparency Log**
A customized activity log gives visibility into platform-level signals such as where accounts are joining from and general usage patterns, without exposing the private content of what individual users are doing.

**Tiered Access**
A clear Free vs Premium structure unlocks enhanced features, cosmetic flourishes, and quality-of-life improvements for supporting members, while keeping the core social experience open to everyone.

---

## 💠 Free vs Premium Tiers

| Capability | Free Tier | Premium Tier |
|---|---|---|
| Posts, comments & reactions | Included | Included |
| Friend requests & messaging | Included | Included |
| Badge & reputation system | Earn standard badges | Earn standard + exclusive badges |
| Group messaging | Limited group size | Expanded group size |
| Profile customization | Standard themes | Full Sci-Fi Viking theme vault |
| Priority support | — | Included |
| Ad-free experience | — | Included |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Server-side Logic | PHP |
| Client-side Behavior | JavaScript, AJAX, DOM manipulation |
| Data Layer | SQL / MySQL |
| Markup & Styling | HTML5, CSS3 |
| UI Framework | Bootstrap 5 |
| Iconography | Font Awesome |
| Typography | Google Fonts |
| Hosting | Namecheap Shared Stellar Hosting, US Datacenter |

---

## 🔐 Security & Encryption Philosophy

Security is treated as a foundational requirement, not an afterthought. Every piece of sensitive data is encrypted before it is ever written to the database, so information is protected at rest as well as in transit. Authentication, session handling, and input validation are being designed with defense-in-depth principles from day one, and the platform is intended to withstand real-world adversarial testing rather than just casual misuse.

Planned safeguards include hashed and salted credential storage, encrypted personal data fields, parameterized database queries to prevent injection attacks, strict server-side input validation, rate limiting on sensitive actions such as login and messaging, and CSRF protection across all state-changing requests.

---

## 🏗️ System Architecture

RavenWarp follows a classic and battle-tested LAMP-adjacent structure suited to shared hosting environments: a PHP application layer handling business logic and authentication, a MySQL relational database for structured storage of users, posts, relationships, messages, and reputation data, and a responsive Bootstrap 5 front end enhanced with AJAX for a smooth, low-latency user experience without a heavy JavaScript framework dependency.

---

## 🎛️ Admin Command Deck

A dedicated admin panel gives platform operators a bird's-eye view of the ecosystem's health without intruding on individual user privacy. It surfaces metrics such as new account registrations over time, new post and comment volume, active session counts, geographic origin trends, and overall platform growth — giving operators the signal they need without the noise of individual user content.

---

## 🏅 Reputation & Badge System

Members earn reputation points through positive platform engagement, which in turn unlocks visual badges displayed on their profile. Badges may reflect account milestones, community contributions, tenure, or special recognitions, giving long-term members a visible, gamified sense of progression and status within the RavenWarp community.

---

## 🗺️ Roadmap

- Finalize database schema and entity relationships
- Build core authentication and encrypted user data pipeline
- Implement posts, comments, and the social graph
- Build real-time private and group messaging
- Implement badge and reputation engine
- Build the admin analytics dashboard
- Conduct internal security hardening pass
- Launch public HackerOne and BugCrowd Vulnerability Disclosure Program
- Public launch on dedicated domain

---

## 🌐 Hosting Environment

RavenWarp is currently developed and deployed on Namecheap Shared Stellar Hosting, featuring unmetered disk space and bandwidth, support for unlimited hosted domains, and a US-based datacenter footprint, providing a cost-effective and scalable foundation for the platform's initial growth phase.

---

## 🤝 Contributing

This repository is being made public as part of an open, transparent AI development experiment. Community feedback, issue reports, and pull requests are welcomed once the initial build reaches a stable milestone. Please watch this space for contribution guidelines as the project matures.

---

## 🛡️ Responsible Disclosure

Once RavenWarp reaches production readiness, a formal Vulnerability Disclosure Program will be launched on HackerOne and BugCrowd, inviting the security research community to responsibly test, probe, and help harden the platform. Details on scope and reporting channels will be published here and on the project website as the program goes live.

---

## 📄 License

This project is released under the MIT License. See the LICENSE file for full details.

---

## 🙏 Acknowledgments

RavenWarp is a product of the Bearded Viking project, born from a love of Norse mythology and Sci-Fi storytelling across Star Wars, Star Trek, Stargate, Rick and Morty, and Back to the Future. Special thanks to the broader AI research community whose tools made this side-by-side experiment possible.

---

## 📡 Connect With Us

<div align="center">

[![Website](https://img.shields.io/badge/Website-beardedviking.org-0d1117?logo=googlechrome&logoColor=white)](https://beardedviking.org)
[![Medium](https://img.shields.io/badge/Read%20on-Medium-000000?logo=medium&logoColor=white)](https://medium.com/)

*Built by an Irish Viking with a love for the stars.* 🪐⚔️

</div>