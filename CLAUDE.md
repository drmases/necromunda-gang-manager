# Necromunda Gang Manager

## Start local dev session

```
cd claude_projects/necromunda-gang-manager && git pull origin claude/pensive-keller-we155g && cd frontend && npm run dev
```

Then open: http://localhost:5173/necromunda-gang-manager/

## Deploy to globbin.se

Upload via FTP:
- `backend/api/*.php` → server backend folder
- Run `npm run build` in `frontend/`, then upload `dist/` contents

## Branch

All Claude Code changes go to: `claude/pensive-keller-we155g`
