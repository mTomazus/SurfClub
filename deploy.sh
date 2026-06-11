#!/bin/bash
# deploy.sh — sync local changes to surfclub.lt server
# Usage: ./deploy.sh

set -e

HOST="45.84.207.193"
PORT="65002"
USER="u704589896"
REMOTE_ROOT="domains/surfclub.lt"
LOCAL_ROOT="$(cd "$(dirname "$0")" && pwd)"

SSH_OPTS="-p $PORT -o StrictHostKeyChecking=no -o PreferredAuthentications=password -o PubkeyAuthentication=no"

if [ -z "$DEPLOY_PASS" ]; then
  read -rsp "Password: " DEPLOY_PASS
  echo
fi

PASSFILE=$(mktemp)
printf '%s' "$DEPLOY_PASS" > "$PASSFILE"
chmod 600 "$PASSFILE"

rsync_up() {
  local src="$1"
  local dest="$2"
  rsync -az --rsh="sshpass -f $PASSFILE ssh $SSH_OPTS" \
    "$LOCAL_ROOT/$src" \
    "$USER@$HOST:$REMOTE_ROOT/$dest"
}

echo "→ Deploying to $HOST…"

# CSS & assets (public/ locally == public_html/ on Hostinger)
rsync_up "public/css/"          "public_html/css/"
rsync_up "public/js/"           "public_html/js/"
rsync_up "public/images/"       "public_html/images/"

# Templates
rsync_up "templates/"           "templates/"

# Modules
rsync_up "modules/"             "modules/"

# NOTE: config/ and engine/ are intentionally NOT deployed.
# config/ holds environment-specific settings (BASE_URL, ENV, DB and
# payment credentials) — syncing the local dev config would break
# production. engine/ is the Trongate core and must stay untouched.

rm -f "$PASSFILE"

echo "✓ Deploy complete."
