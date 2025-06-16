from multiprocessing import Process
import subprocess
import os
import sys

def run_script(script_path):
    """Run a Python script using the virtual environment's interpreter"""
    try:
        # Determine the correct Python path based on OS
        python_exec = os.path.join("venv", "bin", "python") if os.name == 'posix' \
                     else os.path.join("venv", "Scripts", "python.exe")

        # Verify paths exist
        if not os.path.exists(python_exec):
            raise FileNotFoundError(f"Python interpreter not found at {python_exec}")
        if not os.path.exists(script_path):
            raise FileNotFoundError(f"Script not found at {script_path}")
        
        # Run the script
        result = subprocess.run(
            [python_exec, script_path],
            check=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True
        )
        print(f"{script_path} output:\n{result.stdout}")
        
    except subprocess.CalledProcessError as e:
        print(f"Error in {script_path}:\n{e.stderr}")
    except Exception as e:
        print(f"Failed to run {script_path}: {str(e)}")

if __name__ == '__main__':
    # Verify scripts exist before starting processes
    scripts = ["signaling.py", "arima.py"]
    for script in scripts:
        if not os.path.exists(script):
            print(f"Error: Script '{script}' not found!")
            sys.exit(1)
    
    # Create and start processes
    processes = []
    for script in scripts:
        p = Process(target=run_script, args=(script,))
        p.start()
        processes.append(p)
    
    # Wait for all processes to complete
    for p in processes:
        p.join()