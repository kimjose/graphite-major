# !/bin/bash

#if [ -x "$(command -v php)" ]; then
if ! command -v php &> /dev/null
then
    echo "PHP is not installed, installing now..."
    sudo add-apt-repository ppa:ondrej/php;
    sudo apt update
    sudo apt install php8.2 php8.2-curl;
    sudo phpenmod curl;
    echo "PHP installed successful...";
else
    echo "PHP is already installed."
fi

# Directory path to create and check
directory="/CONNECT/KenyaEMR/FacilityName"

# Create the directory if it doesn't exist
if [ ! -d "$directory" ]; then
  mkdir -p "$directory"
  echo "Directory created: $directory"
  sudo chmod -R 700 /CONNECT/KenyaEMR/FacilityName
else
  echo "Directory already exists: $directory"
fi

# Create scheduler
if
    sudo crontab -l | { cat; echo "0  17 * * * php /root/system-backup/upload_file.php"; } | crontab -
    echo "scheduler created successfully..."
else
    echo "scheduler not created."
fi
# Check if PHP is installed
#if [ -x "$(command -v php)" ]; then
#    echo "PHP is already installed."
#else
# Install PHP
#    sudo apt-get update
#    sudo apt-get install php
#    echo "PHP has been installed."
#fi

##
#!/bin/bash

#if ! [ -x "$(command -v php8.2)" ]; then
#if [ $(dpkg-query -W -f='${Status}' php8.4 2>/dev/null | grep -c "ok installed") -eq 0 ];
#then
#  echo "PHP is not installed. Installing PHP...";
#    sudo add-apt-repository ppa:ondrej/php;
#    sudo apt update;
#    sudo apt install php8.2 php8.2-curl;
#    sudo phpenmod curl;
#  echo "PHP installed successful...";
#else
#  echo "PHP is already installed.";
#fi


