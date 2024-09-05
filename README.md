# PHP fun

A fun php example.

## In order to run

1. Install colima and docker and make sure it's all on native arm64 

```bash
arch -arm64 brew install docker colima
```

2. Start colima

```bash
colima start --vm-type=vz
```

3. Set permissions for scripts

```bash
chmod +x START.sh && chmod +x STOP.sh
```

3. Run LAMP stack docker image

```bash
./START.sh
```

4. Stop LAMP stack image

```bash
./STOP.sh
```
