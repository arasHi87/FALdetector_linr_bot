FALdetector_line_bot
====================

environment
-----------
ubuntu 18.04 
python 3

install docker
--------------
First, update your existing list of packages:
```
sudo apt update
```
Next, install a few prerequisite packages which let apt use packages over HTTPS:
```
sudo apt install apt-transport-https ca-certificates curl software-properties-common
```
Then add the GPG key for the official Docker repository to your system:
```
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
```
Add the Docker repository to APT sources:
```
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu bionic stable"
```
Next, update the package database with the Docker packages from the newly added repo:
```
sudo apt update
```
Finally, install Docker:
```
sudo apt install docker-ce
```
## get docker image
and then you need to run following command to get the docker image i make:
```
sudo docker pull arashi87/ai-sys:FALdetector_sys
```
## make dir
make dir in same place with this project:
```
sudo mkdir images
```
## run image
finally run following code to run the image
```
sudo docker exec ai-sys bash -c "source /root/virenv/AI_TEST/bin/activate && python /root/FALdetector/local_detector.py --input_path examples/test.jpg --model_path weights/local.pth --dest_folder images --no_crop"
```
