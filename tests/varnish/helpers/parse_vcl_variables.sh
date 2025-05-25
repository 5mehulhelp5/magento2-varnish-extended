# generate usable VCL pointing towards s1
# mostly, we replace the place-holders, but we also jack up the probe
# interval to avoid further interference

# Backend configuration
export HOST="${s1_addr}"
export PORT="${s1_port}"
export GRACE_PERIOD="300"

# SSL configuration
export SSL_OFFLOADED_HEADER="X-Forwarded-Proto"

# Feature flags
export USE_XKEY_VMOD="1"
export ENABLE_MEDIA_CACHE="1"
export ENABLE_STATIC_CACHE="1"

# ACL list with IPs
export ACCESS_LIST="server1 server2"
export SERVER1_IP="${s1_addr}"
export SERVER2_IP="10.0.0.2"

# Cookie list with regex patterns
export PASS_ON_COOKIE_PRESENCE="cookie1 cookie2"
export COOKIE1_REGEX="^ADMIN"
export COOKIE2_REGEX="^PHPSESSID"

# Performance parameters
export TRACKING_PARAMETERS="utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl"

# Design exceptions
export DESIGN_EXCEPTIONS_CODE='if (req.url ~ "^/media/theme/") { hash_data("design1"); }'