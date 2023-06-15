#!/bin/bash


# Penser a installer le client NcFTP avant :
# https://www.ncftp.com/download/
# prendre client pour Windows

HOST='XXX'
USER="XXX"
PASSWORD="XXX"

ncftp u $USER -p $PASSWORD $HOST