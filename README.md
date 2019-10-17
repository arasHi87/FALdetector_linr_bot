# FALdetector_line_bot
You need to get the docker first
yoou can run this to get my docker image
```
sudo docker pull arashi87/ai-sys:FALdetector_sys
```
And after you finish it, run following command to exec the image
```
sudo docker exec ai-sys bash -c "source /root/virenv/AI_TEST/bin/activate && python /root/FALdetector/local_detector.py --input_path examples/test.jpg --model_path weights/local.pth --dest_folder images --no_crop"
```
