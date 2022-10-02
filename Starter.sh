#!/bin/bash
clear

#            ▄▀█ █▀▄▀█ █▀█ █▀█ █▀▀
#            █▀█ █░▀░█ █▄█ █▀▄ ██▄
#
#              © Copyright 2022
#
#         https://t.me/the_farkhodov
#
# 🔒 Licensed under the GNU GPLv3
# 🌐 https://www.gnu.org/licenses/agpl-3.0.html

printf "\n░█▀▀█ ░█▀▄▀█ ░█▀▀▀█ ░█▀▀█ ░█▀▀▀ 　 ░█▀▀▀█ ▀▀█▀▀ ─█▀▀█ ░█▀▀█ ▀▀█▀▀ ░█▀▀▀ ░█▀▀█"
printf "\n░█▄▄█ ░█░█░█ ░█──░█ ░█▄▄▀ ░█▀▀▀ 　 ─▀▀▀▄▄ ─░█── ░█▄▄█ ░█▄▄▀ ─░█── ░█▀▀▀ ░█▄▄▀"
printf "\n░█─░█ ░█──░█ ░█▄▄▄█ ░█─░█ ░█▄▄▄ 　 ░█▄▄▄█ ─░█── ░█─░█ ░█─░█ ─░█── ░█▄▄▄ ░█─░█"
printf "\n Starting hikka on Goorm...\n\n"

# upgrade
cd && sudo apt update -y && sudo apt upgrade -y

# install requemerments
sudo apt install python3.8 -y && sudo apt install tmux -y && sudo apt install git -y && sudo apt install python3.8-distutils -y

# start userbot 
git clone https://github.com/hikariatama/Hikka && curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py && python3.8 get-pip.py && cd Hikka && python3.8 -m pip install -r requirements.txt