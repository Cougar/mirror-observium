#!/bin/bash
lessc -x html/css/bootstrap/less/bootstrap.less > html/css/bootstrap.css
sed -i 's|../../font-awesome/less/||g' html/css/bootstrap.css


#lessc -x html/css/bootstrap/less/bootstrap-email.less > html/css/bootstrap-email.css

