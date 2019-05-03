const readQueue : Array<Function> = [];

const writeQueue : Array<Function> = [];

/**
 * Whether a new animation frame has already been
 * requested
 */
let isRunning : boolean = false;

/**
 * Runs each action in the given queue in order,
 * buffered, to prevent race conditions
 * 
 * @param queue 
 */
function processQueue(queue : Array<Function>) {
    const size = queue.length;
    for(let i = 0; i < size; i++) {
        const action = queue.shift();
        action();
    }
}

/**
 * Processes the read queue and then the write queue
 * for efficiency
 */
function run() : void {
    processQueue(readQueue);
    processQueue(writeQueue);
    isRunning = false;

    // if new actions were inserted before the queue
    // fully processed, request another run next animation
    // frame
    if(readQueue.length > 0 || writeQueue.length > 0) {
        requestRun();
    }
}

/**
 * Request for the queue to be processed on the next 
 * available animation frame
 */
function requestRun() : void {
    if(!isRunning) {
        isRunning = true;
        requestAnimationFrame(run);
    }
}

/**
 * Queues a DOM read action to run on the next available
 * animation frame
 * 
 * @param action
 */
export function queueRead(action : Function) : void {
    readQueue.push(action);
    requestRun();
}

/**
 * Queues a DOM write action to run on the next available
 * animation frame
 * 
 * @param action 
 */
export function queueWrite(action : Function) : void {
    writeQueue.push(action);
    requestRun();
}