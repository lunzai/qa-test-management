
## CHANGE LOG

**#TODO**
- Forget password
- Admin
    - Change user password
    - Leave mgmt + calendar integartion
- Clone group without test result - for deployment test run
- Auto complete / suggest for table filter field
- Move qa.wc-app.com to tech.wc-app.com
- Audit for timeline and maybe other thing
- calendar add milestone
- calendar end date = next day problem
- BUG: when all success, add failed result, result become pending instead of failed
- issue > filter test case by status etc...

**v1.0.11 - 2 Jun 2020 - HL**
- Add search to test case
- Replace FA with FA, because FA CDN wols
- Add delete to group, issue, test case

**v1.0.10 - 1 Jun 2020 - HL**
- bug when added timeline - appear 2, should be related to auto update not overriding the old one

**v1.0.9 - 30 May 2020 - HL**
- Add status component into listing page
- Add listing test count component

**v1.0.8 - 29 May 2020 - HL**
- add jira_number column to group view page -> issue listing
- admin: country, holiday CRUD

**v1.0.7 - 28 May 2020 - HL**
- Change cookie and session config
- background update for timeline page

**v1.0.6 - 26 May 2020 - HL**
- Lark integration

**v1.0.5 - 25 May 2020 - HL**
- Admin
    - User mgmt, add Role
    - can disable user account
- Login now check for disabled / active account
- Work timetable - calendar see tui.calendar
	- Add holiday
	- User can add work log

**v1.0.4 - 21 May 2020 - HL**
- Add deploy status to group & issue
- Add my issues page
- Fix move issue recalculate group count

**v1.0.3 - 21 May 2020 - HL**
- star/unstar group
- Replace dashboard with shortcut page to starred group
  
**v1.0.2 - 16 May 2020 - HL**
- Allow changing of group when update issue
- Add link to lark spec URL in issue view page
- Fixed breadcrumb in Create test case page
- Change auto scrolling in issue view page to scroll to tab for result, temp fix
- Test result version is now optional
- Reworked JIRA integration logic, failed comment remained in test result level, pass moved to issue level