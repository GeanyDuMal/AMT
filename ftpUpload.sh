#!/bin/bash

HOST='XXX'
USER="XXX"
PASSWORD="XXX"


REMOTE_SRC="src"
LOCAL_ENTITY="src/Entity/*.php"
REMOTE_ENTITY="Entity"

LOCAL_MANAGER="src/Manager/*.php"
REMOTE_MANAGER="Manager"

LOCAL_REPOSITORY="src/Repository/*.php"
REMOTE_REPOSITORY="Repository"

LOCAL_UTILS="src/Utils/*.php"
REMOTE_UTILS="Utils"

LOCAL_UTILS="src/Controller/*.php"
REMOTE_UTILS="Utils"

ftp -n $HOST <<END_SCRIPT
quote PASS $PASSWORD


mkdir $REMOTE_SRC
cd $REMOTE_SRC || exit

mkdir $REMOTE_ENTITY
cd $REMOTE_ENTITY || exit
mput "$LOCAL_ENTITY"














cd ..
mkdir $REMOTE_MANAGER
cd $REMOTE_MANAGER || exit
mput "$LOCAL_MANAGER"















cd ..
mkdir $REMOTE_REPOSITORY
cd $REMOTE_REPOSITORY || exit
mput "$LOCAL_REPOSITORY"














cd ..
mkdir $REMOTE_UTILS
cd $REMOTE_UTILS || exit
mput "$LOCAL_UTILS"














cd ..
mkdir $REMOTE_UTILS
cd $REMOTE_UTILS || exit
mput "$LOCAL_UTILS"












#Dossier src sauf Controller
END_SCRIPT
$SHELL